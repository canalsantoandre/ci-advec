<?php

namespace App\Services;

use App\Models\DepartamentoModel;
use App\Services\WebhookService;
use Throwable;

class AgendaNotificacaoService
{
    /**
     * Dispara notificação via WhatsApp para o grupo do departamento responsável por artes/comunicação
     * quando um novo convidado é vinculado a um culto/evento na Agenda.
     *
     * @param int $id_culto
     * @param int $id_convidado
     * @return array
     */
    public static function notificarNovoConvidadoAgendado(int $id_culto, int $id_convidado): array
    {
        try {
            $db = db_connect();

            // 1. Busca os dados do Culto / Evento
            $culto = $db->table('tb_agenda_culto')
                ->where('id_culto', $id_culto)
                ->get()
                ->getFirstRow();

            if (!$culto) {
                log_message('warning', "[AgendaNotificacaoService] Culto {$id_culto} não encontrado.");
                return ['success' => false, 'message' => 'Culto não encontrado.'];
            }

            // 2. Busca os dados do Convidado com sua função eclesiástica
            $convidado = $db->table('tb_convidado as c')
                ->select('
                    c.id_convidado,
                    c.nome_convidado,
                    c.nome_convidado_visualizacao,
                    c.nick_instagram,
                    c.link_fotos,
                    f.nm_funcao_eclesiastica
                ')
                ->join('tb_funcao_eclesiastica as f', 'f.id_funcao_eclesiastica = c.id_funcao_eclesiastica', 'left')
                ->where('c.id_convidado', $id_convidado)
                ->get()
                ->getFirstRow();

            if (!$convidado) {
                log_message('warning', "[AgendaNotificacaoService] Convidado {$id_convidado} não encontrado.");
                return ['success' => false, 'message' => 'Convidado não encontrado.'];
            }

            // 3. Identifica os departamentos configurados para receber notificações de agenda
            $departamentoModel = new DepartamentoModel();
            $departamentos = $departamentoModel->getDepartamentosNotificacaoAgenda();

            // Fallback: se nenhum departamento estiver com receber_notificacao_agenda=1, busca departamento de Comunicação com whatsapp_group_id
            if (empty($departamentos)) {
                $departamentos = $departamentoModel->where('status', 1)
                    ->groupStart()
                        ->like('nome', 'Comunicação')
                        ->orLike('nome', 'Comunicacao')
                        ->orLike('nome', 'Arte')
                        ->orLike('nome', 'Mídia')
                        ->orLike('nome', 'Midia')
                    ->groupEnd()
                    ->where('whatsapp_group_id IS NOT NULL')
                    ->where('whatsapp_group_id !=', '')
                    ->findAll();
            }

            if (empty($departamentos)) {
                log_message('info', '[AgendaNotificacaoService] Nenhum departamento configurado com whatsapp_group_id para receber notificações de agenda.');
                return [
                    'success' => false,
                    'message' => 'Nenhum grupo de WhatsApp configurado para receber notificações de artes/agenda.'
                ];
            }

            // 4. Monta os dados para o corpo da mensagem
            $nomeCulto = !empty($culto->titulo_culto) ? trim($culto->titulo_culto) : 'Culto Especial';
            
            // Data e Horário
            $dataFmt = !empty($culto->data_culto) ? date('d/m/Y', strtotime($culto->data_culto)) : 'Data a definir';
            $horaFmt = !empty($culto->horario_inicio) ? date('H:i', strtotime($culto->horario_inicio)) : 'Horário a definir';

            // Tema / Observações do evento
            $temaCulto = !empty(trim((string)$culto->descricao)) ? trim($culto->descricao) : 'Não informado';

            // Dados do Convidado (LGPD: Telefone e Email estritamente omitidos)
            $nomeConvidado = !empty(trim((string)$convidado->nome_convidado))
                ? trim($convidado->nome_convidado)
                : (!empty($convidado->nome_convidado_visualizacao) ? trim($convidado->nome_convidado_visualizacao) : 'Convidado');

            $funcaoEclesiastica = !empty(trim((string)$convidado->nm_funcao_eclesiastica))
                ? trim($convidado->nm_funcao_eclesiastica)
                : 'Convidado';

            // Redes Sociais
            $redesSociais = 'Não informado';
            if (!empty(trim((string)$convidado->nick_instagram))) {
                $nick = trim($convidado->nick_instagram);
                if (stripos($nick, 'http') === 0) {
                    $redesSociais = $nick;
                } else {
                    $cleanNick = ltrim($nick, '@');
                    $redesSociais = "@{$cleanNick} (https://instagram.com/{$cleanNick})";
                }
            }

            // Material / Fotos para criação das artes
            $linksFotos = !empty(trim((string)$convidado->link_fotos))
                ? trim($convidado->link_fotos)
                : 'Não informado';

            // 5. Template Padrão com Markdown do WhatsApp
            $mensagem = "📢 *NOVO CONVIDADO AGENDADO — SOLICITAÇÃO DE ARTE*\n\n"
                      . "Olá, equipe! Um novo convidado foi adicionado à agenda e precisa da criação de artes.\n\n"
                      . "📅 *Informações do Culto:*\n"
                      . "• *Evento:* {$nomeCulto}\n"
                      . "• *Data/Hora:* {$dataFmt} às {$horaFmt}\n"
                      . "• *Tema:* {$temaCulto}\n\n"
                      . "👤 *Dados do Convidado:*\n"
                      . "• *Nome:* {$nomeConvidado}\n"
                      . "• *Função:* {$funcaoEclesiastica}\n"
                      . "• *Redes Sociais:* {$redesSociais}\n"
                      . "• *Material/Fotos:* {$linksFotos}\n\n"
                      . "Por favor, providenciem a arte com base nessas informações.";

            $envios = [];
            $erros = [];

            // 6. Disparo assíncrono/não-bloqueante para cada grupo configurado
            foreach ($departamentos as $dep) {
                $groupId = trim((string)$dep->whatsapp_group_id);
                if (empty($groupId)) {
                    continue;
                }

                try {
                    $res = WebhookService::dispararMensagemWhatsApp($groupId, $mensagem);
                    $envios[] = [
                        'departamento' => $dep->nome,
                        'group_id'     => $groupId,
                        'resultado'    => $res
                    ];
                } catch (Throwable $eEnvio) {
                    log_message('error', "[AgendaNotificacaoService] Erro ao enviar WhatsApp para o grupo {$groupId} do departamento {$dep->nome}: " . $eEnvio->getMessage());
                    $erros[] = [
                        'departamento' => $dep->nome,
                        'group_id'     => $groupId,
                        'erro'         => $eEnvio->getMessage()
                    ];
                }
            }

            return [
                'success' => true,
                'envios'  => $envios,
                'erros'   => $erros
            ];

        } catch (Throwable $e) {
            // Garante que falhas nunca travem a persistência principal da agenda
            log_message('error', '[AgendaNotificacaoService] Falha geral ao processar notificação de agenda: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar notificação: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Processa a notificação para múltiplos convidados vinculados
     *
     * @param int $id_culto
     * @param array $ids_convidados
     * @return array
     */
    public static function notificarNovosConvidadosAgendados(int $id_culto, array $ids_convidados): array
    {
        $resultados = [];
        foreach ($ids_convidados as $idConvidado) {
            $idConvidado = (int)$idConvidado;
            if ($idConvidado > 0) {
                $resultados[$idConvidado] = self::notificarNovoConvidadoAgendado($id_culto, $idConvidado);
            }
        }
        return $resultados;
    }
}
