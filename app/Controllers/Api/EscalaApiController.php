<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class EscalaApiController extends BaseController
{
    /**
     * Retorna a lista de voluntários com escalas pendentes de confirmação
     * GET /api/v1/escalas/lembretes-pendentes
     */
    public function lembretesPendentes()
    {
        $db = db_connect();

        $builder = $db->table('tb_escala_voluntario as ev');
        $builder->select('
            ev.id_escala_voluntario,
            ev.id_voluntario,
            ev.id_departamento,
            ev.id_area,
            ev.data_culto,
            ev.status_confirmacao,
            ev.data_ultimo_lembrete,
            v.nome as nome_voluntario,
            v.nickname,
            v.telefone_whatsapp,
            d.nome as nome_departamento,
            a.nome_area,
            COALESCE(cp.nome_culto, c.titulo_culto, "Culto") as titulo_culto,
            COALESCE(cp.horario_inicio, c.horario_inicio, "00:00:00") as horario_inicio,
            COALESCE(cp.horario_termino, c.horario_termino, "") as horario_termino
        ');
        $builder->join('tb_voluntario as v', 'v.id_voluntario = ev.id_voluntario', 'inner');
        $builder->join('tb_departamento as d', 'd.id_departamento = ev.id_departamento', 'inner');
        $builder->join('tb_departamento_area as a', 'a.id_area = ev.id_area', 'inner');
        $builder->join('tb_culto_padrao as cp', 'cp.id_culto_padrao = ev.id_culto_padrao', 'left');
        $builder->join('tb_agenda_culto as c', 'c.id_culto = ev.id_culto', 'left');

        // 1. Apenas voluntários ativos e aprovados
        $builder->where('v.status', 1);
        $builder->where("COALESCE(v.status_aprovacao, 'APROVADO') = 'APROVADO'");
        $builder->where("v.telefone_whatsapp IS NOT NULL AND v.telefone_whatsapp != ''");

        // 2. Apenas escalas pendentes de confirmação
        $builder->where("UPPER(COALESCE(ev.status_confirmacao, 'PENDENTE')) = 'PENDENTE'");

        // Filtro opcional: por departamento (se informado via GET ?id_departamento=1)
        $idDepFiltro = $this->request->getGet('id_departamento');
        if (!empty($idDepFiltro)) {
            $builder->where('ev.id_departamento', (int)$idDepFiltro);
        }

        // 3. Cultos futuros ou de hoje que ainda não terminaram
        $agoraDataHora = date('Y-m-d H:i:s');
        $hoje = date('Y-m-d');
        $builder->where('ev.data_culto >=', $hoje);

        // 4. Anti-spam: Ignora quem já recebeu lembrete hoje (a menos que passe ?ignorar_antispam=1 para testes)
        $ignorarAntiSpam = (bool)$this->request->getGet('ignorar_antispam');
        if (!$ignorarAntiSpam) {
            $builder->groupStart()
                ->where('ev.data_ultimo_lembrete IS NULL')
                ->orWhere('DATE(ev.data_ultimo_lembrete) <', $hoje)
                ->groupEnd();
        }

        $builder->orderBy('ev.data_culto', 'ASC');
        $builder->orderBy('cp.horario_inicio', 'ASC');
        $builder->orderBy('v.nome', 'ASC');

        $escalas = $builder->get()->getResult('object');

        // Agrupamento inteligente por voluntário (evita duplicar mensagens no mesmo WhatsApp)
        $porVoluntario = [];
        $totalEscalas = 0;
        $linkPortal = base_url('portal/agenda');

        foreach ($escalas as $esc) {
            $horaInicio = !empty($esc->horario_inicio) ? $esc->horario_inicio : '00:00:00';
            $cultoDataHora = $esc->data_culto . ' ' . $horaInicio;
            if ($cultoDataHora <= $agoraDataHora) {
                // Culto já ocorrido ou em andamento
                continue;
            }

            $idVol = (int)$esc->id_voluntario;
            $foneLimpo = preg_replace('/\D/', '', (string)$esc->telefone_whatsapp);
            if (strlen($foneLimpo) < 10) {
                continue;
            }

            if (!isset($porVoluntario[$idVol])) {
                $primeiroNome = explode(' ', trim($esc->nome_voluntario))[0];
                $porVoluntario[$idVol] = [
                    'id_voluntario'     => $idVol,
                    'nome_completo'     => $esc->nome_voluntario,
                    'primeiro_nome'     => $primeiroNome,
                    'telefone_whatsapp' => $foneLimpo,
                    'departamento'      => $esc->nome_departamento,
                    'ids_escala'        => [],
                    'cultos'            => []
                ];
            }

            $dataFormatada = date('d/m/Y', strtotime($esc->data_culto));
            $horaFormatada = substr($horaInicio, 0, 5);

            $porVoluntario[$idVol]['ids_escala'][] = (int)$esc->id_escala_voluntario;
            $porVoluntario[$idVol]['cultos'][] = [
                'id_escala'      => (int)$esc->id_escala_voluntario,
                'data'           => $esc->data_culto,
                'data_formatada' => $dataFormatada,
                'horario'        => $horaFormatada,
                'culto'          => $esc->titulo_culto,
                'area'           => $esc->nome_area
            ];

            $totalEscalas++;
        }

        // Busca instâncias ativas do WhatsApp (Evolution API)
        $instanciasPorNome = [];
        $instanciaPadrao = '';
        if ($db->tableExists('whatsapp_configs')) {
            $configs = $db->table('whatsapp_configs')
                ->where('deleted_at IS NULL')
                ->where('connected', 1)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResult();
            foreach ($configs as $cfg) {
                if (empty($instanciaPadrao)) {
                    $instanciaPadrao = $cfg->instance_name;
                }
                if (!empty($cfg->name)) {
                    $instanciasPorNome[strtoupper(trim($cfg->name))] = $cfg->instance_name;
                }
            }
            if (empty($instanciaPadrao)) {
                $firstCfg = $db->table('whatsapp_configs')
                    ->where('deleted_at IS NULL')
                    ->orderBy('id', 'ASC')
                    ->get(1)
                    ->getRow();
                if ($firstCfg && !empty($firstCfg->instance_name)) {
                    $instanciaPadrao = $firstCfg->instance_name;
                }
            }
        }
        if (empty($instanciaPadrao)) {
            $instanciaPadrao = 'advec_wa_85250';
        }

        // Gera a mensagem pronta formatada para cada voluntário
        $resultado = [];
        foreach ($porVoluntario as $vol) {
            $linhasCultos = [];
            foreach ($vol['cultos'] as $c) {
                $linhasCultos[] = "• 📅 *{$c['data_formatada']} ({$c['horario']})* - {$c['culto']} ({$c['area']})";
            }
            $blocoCultos = implode("\n", $linhasCultos);

            $msg = "ADVEC: Olá, *{$vol['primeiro_nome']}*! 🙌 Graça e paz!\n\n"
                 . "Passando para lembrar que você foi escalado(a) no departamento *{$vol['departamento']}*:\n\n"
                 . "{$blocoCultos}\n\n"
                 . "⚠️ *Atenção:* Precisamos da sua confirmação com antecedência para fechamento da equipe.\n\n"
                 . "Por favor, acesse o link abaixo para *CONFIRMAR* sua presença ou informar uma justificativa:\n"
                 . "👉 {$linkPortal}\n\n"
                 . "Contamos muito com você! Deus abençoe seu dia! 🙏🔥";

            $nomeDepUpper = strtoupper(trim((string)$vol['departamento']));
            $instanciaVol = $instanciasPorNome[$nomeDepUpper] ?? $instanciaPadrao;

            // Formata telefone com DDI 55 caso não tenha
            $foneLimpo = preg_replace('/\D/', '', (string)$vol['telefone_whatsapp']);
            $foneComDdi = (strpos($foneLimpo, '55') === 0) ? $foneLimpo : ('55' . $foneLimpo);
            $remoteJid = $foneComDdi . '@s.whatsapp.net';

            $vol['total_escalas']   = count($vol['ids_escala']);
            $vol['link_portal']     = $linkPortal;
            $vol['mensagem_pronta'] = $msg;
            $vol['mensagem']        = $msg;
            $vol['instance']        = $instanciaVol;
            $vol['WHATSAPP']        = $remoteJid;
            $vol['remoteJid']       = $remoteJid;
            $vol['telefone_destino']= $foneComDdi;
            $resultado[] = $vol;
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status'           => 'success',
            'instancia_padrao' => $instanciaPadrao,
            'total_voluntarios'=> count($resultado),
            'total_escalas'    => $totalEscalas,
            'gerado_em'        => date('Y-m-d H:i:s'),
            'dados'            => $resultado
        ]);
    }

    /**
     * Registra que o lembrete foi enviado com sucesso, atualizando data_ultimo_lembrete
     * POST /api/v1/escalas/marcar-lembrete
     */
    public function marcarLembrete()
    {
        $input = $this->request->getJSON(true);
        if (empty($input)) {
            $input = $this->request->getPost();
        }

        $ids = $input['ids_escala'] ?? $input['id_escala'] ?? [];
        if (!is_array($ids)) {
            $ids = !empty($ids) ? explode(',', (string)$ids) : [];
        }

        $idsLimpos = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (empty($idsLimpos)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'code'    => 'EMPTY_IDS',
                'message' => 'Por favor, informe a lista de ids_escala para dar baixa no lembrete.'
            ]);
        }

        $db = db_connect();
        $afetados = $db->table('tb_escala_voluntario')
            ->whereIn('id_escala_voluntario', $idsLimpos)
            ->update([
                'data_ultimo_lembrete' => date('Y-m-d H:i:s')
            ]);

        return $this->response->setStatusCode(200)->setJSON([
            'status'            => 'success',
            'escalas_atualizadas'=> $afetados,
            'message'           => "Lembrete marcado como enviado com sucesso para {$afetados} escala(s)."
        ]);
    }
}
