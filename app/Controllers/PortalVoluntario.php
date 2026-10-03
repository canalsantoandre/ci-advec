<?php

namespace App\Controllers;

use App\Models\VoluntarioModel;
use App\Models\VoluntarioAreaModel;
use App\Models\VoluntarioCultoModel;
use App\Models\CultoPadraoModel;
use App\Models\EscalaVoluntarioModel;
use App\Services\WebhookService;

class PortalVoluntario extends BaseController
{
    /**
     * Recupera o voluntário autenticado da sessão
     */
    private function getVoluntarioSessao()
    {
        $session = session();
        $sessData = $session->get('dsh_voluntario');
        if (empty($sessData) || empty($sessData['id_voluntario'])) {
            return null;
        }

        $voluntarioModel = new VoluntarioModel();
        $vol = $voluntarioModel->find((int)$sessData['id_voluntario']);
        if (!$vol || $vol->status != 1 || (isset($vol->status_aprovacao) && $vol->status_aprovacao === 'PENDENTE')) {
            $session->remove('dsh_voluntario');
            $session->remove('otp_auth_challenge');
            return null;
        }

        $voluntarioAreaModel = new VoluntarioAreaModel();
        $vol->areas = $voluntarioAreaModel->getAreasDoVoluntario($vol->id_voluntario);
        return $vol;
    }

    /**
     * Tela e Processamento de Login do Voluntário
     */
    public function login()
    {
        $session = session();
        if ($session->has('dsh_voluntario') && !empty($session->get('dsh_voluntario')['logged_in'])) {
            $idVol = (int)($session->get('dsh_voluntario')['id_voluntario'] ?? 0);
            $voluntarioModel = new VoluntarioModel();
            $vol = $idVol > 0 ? $voluntarioModel->find($idVol) : null;
            if ($vol && $vol->status == 1 && (!isset($vol->status_aprovacao) || $vol->status_aprovacao === 'APROVADO')) {
                return redirect()->to(base_url('portal/agenda'));
            } else {
                $session->remove('dsh_voluntario');
                $session->remove('otp_auth_challenge');
            }
        }

        $erroFlash = $session->getFlashdata('erro_login') ?: $session->getFlashdata('erro');

        $data = [
            'title'     => 'Acesso do Voluntário - ADVEC',
            'telefone'  => '',
            'erro'      => $erroFlash ?: ''
        ];

        if (strtolower($this->request->getMethod()) === 'post') {
            $telefone = trim((string)$this->request->getPost('txtTelefone'));
            $senha    = trim((string)$this->request->getPost('txtSenha'));

            $data['telefone'] = $telefone;

            if (empty($telefone) || empty($senha)) {
                $data['erro'] = 'Por favor, informe seu telefone de contato e senha.';
                return view('portal_voluntario/login', $data);
            }

            $voluntarioModel = new VoluntarioModel();
            $voluntario = $voluntarioModel->buscarPorTelefone($telefone);

            if (!$voluntario) {
                $data['erro'] = 'Voluntário não encontrado com o telefone informado.';
                return view('portal_voluntario/login', $data);
            }

            if ($voluntario->status != 1 || (isset($voluntario->status_aprovacao) && $voluntario->status_aprovacao === 'PENDENTE')) {
                if (isset($voluntario->status_aprovacao) && $voluntario->status_aprovacao === 'PENDENTE') {
                    $data['erro'] = 'Seu pré-cadastro foi recebido e está aguardando aprovação do líder do departamento. Você receberá uma notificação assim que for liberado!';
                } elseif (isset($voluntario->status_aprovacao) && $voluntario->status_aprovacao === 'REJEITADO') {
                    $data['erro'] = 'Sua solicitação de cadastro não foi aprovada. Fale com o líder do seu departamento.';
                } else {
                    $data['erro'] = 'Seu cadastro de voluntário está inativo. Fale com o líder do seu departamento.';
                }
                return view('portal_voluntario/login', $data);
            }

            // Validação de senha flexível (aceita hash gravado, formato original, ou dígitos puros)
            $digitosTelefone = preg_replace('/\D/', '', (string)$voluntario->telefone_whatsapp);
            $digitosTelefoneSem55 = (strpos($digitosTelefone, '55') === 0 && strlen($digitosTelefone) >= 12) ? substr($digitosTelefone, 2) : $digitosTelefone;
            $senhaLimpa = preg_replace('/\D/', '', $senha);

            $senhaValida = false;
            $precisaTrocarSenha = (!empty($voluntario->force_pwd_change) || !empty($voluntario->primeiro_acesso));

            if (!empty($voluntario->senha)) {
                if (password_verify($senha, $voluntario->senha) || (!empty($senhaLimpa) && password_verify($senhaLimpa, $voluntario->senha))) {
                    $senhaValida = true;
                } elseif ($precisaTrocarSenha && (
                    $senha === $digitosTelefone || 
                    $senha === $digitosTelefoneSem55 || 
                    $senhaLimpa === $digitosTelefone || 
                    $senhaLimpa === $digitosTelefoneSem55 ||
                    $senha === (string)$voluntario->telefone_whatsapp
                )) {
                    // Senha padrão inicial é aceita se ainda não tiver concluído a troca obrigatória
                    $senhaValida = true;
                }
            } else {
                // Sem hash gravado ainda: aceita a senha padrão inicial e força a troca
                if ($senha === $digitosTelefone || $senha === $digitosTelefoneSem55 || $senhaLimpa === $digitosTelefone || $senhaLimpa === $digitosTelefoneSem55 || $senha === (string)$voluntario->telefone_whatsapp) {
                    $senhaValida = true;
                    $precisaTrocarSenha = true;
                }
            }

            if (!$senhaValida) {
                $data['erro'] = 'Senha incorreta. Caso seja seu primeiro acesso, sua senha inicial é o seu número de WhatsApp.';
                return view('portal_voluntario/login', $data);
            }

            // ============================================================
            // INTERCEPTAÇÃO DE SEGURANÇA: TROCA OBRIGATÓRIA DE SENHA (OTP)
            // ============================================================
            $precisaTrocarSenha = (!empty($voluntario->force_pwd_change) || !empty($voluntario->primeiro_acesso));

            if ($precisaTrocarSenha) {
                // Gera código OTP de 6 dígitos válido por 10 minutos
                $otpCode = $voluntarioModel->gerarOtpTrocaSenha($voluntario->id_voluntario);

                // Dispara mensagem via Webhook para o WhatsApp do voluntário
                $envio = WebhookService::enviarOtpTrocaSenha($voluntario->telefone_whatsapp, $otpCode, $voluntario->nome);

                // Grava sessão temporária de desafio OTP (sem liberar acesso ao sistema)
                $session->set('otp_auth_challenge', [
                    'id_voluntario'     => (int)$voluntario->id_voluntario,
                    'nome'              => $voluntario->nome,
                    'telefone'          => $voluntario->telefone_whatsapp,
                    'solicitado_em'     => time(),
                    'webhook_status'    => $envio['success']
                ]);

                if (!$envio['success']) {
                    session()->setFlashdata('info_otp', 'Aviso: Código de segurança gerado. Caso não receba no WhatsApp, verifique suas configurações ou solicite o reenvio.');
                }

                return redirect()->to(base_url('portal/verificar-otp'));
            }

            // Atualiza data do último login
            $voluntarioModel->update($voluntario->id_voluntario, [
                'data_ultimo_login' => date('Y-m-d H:i:s')
            ]);

            // Grava sessão exclusiva do voluntário
            $sessionData = [
                'id_voluntario' => $voluntario->id_voluntario,
                'nome'          => $voluntario->nome,
                'nickname'      => $voluntario->nickname,
                'email'         => $voluntario->email,
                'telefone'      => $voluntario->telefone_whatsapp,
                'foto_url'      => $voluntario->foto_url,
                'logged_in'     => true,
                'role'          => 'VOLUNTARIO'
            ];

            $session->set('dsh_voluntario', $sessionData);

            return redirect()->to(base_url('portal/agenda'));
        }

        return view('portal_voluntario/login', $data);
    }

    /**
     * Tela de Verificação de OTP e Definição de Nova Senha Obrigatória
     */
    public function verificarOtp()
    {
        $session = session();
        $challenge = $session->get('otp_auth_challenge');

        if (empty($challenge) || empty($challenge['id_voluntario'])) {
            return redirect()->to(base_url('portal/login'));
        }

        $telefone = (string)($challenge['telefone'] ?? '');
        $digits = preg_replace('/\D/', '', $telefone);
        $telefoneMascarado = $telefone;

        if (strlen($digits) >= 10) {
            $ddd = substr($digits, 0, 2);
            $fim = substr($digits, -4);
            $telefoneMascarado = "({$ddd}) 9****-{$fim}";
        }

        $data = [
            'title'             => 'Validação de Segurança OTP - ADVEC',
            'challenge'         => $challenge,
            'telefoneMascarado' => $telefoneMascarado,
            'erro'              => ''
        ];

        return view('portal_voluntario/trocar-senha-otp', $data);
    }

    /**
     * Processa a confirmação de troca de senha via código OTP
     */
    public function confirmarTrocaSenhaOtp()
    {
        $session = session();
        $challenge = $session->get('otp_auth_challenge');

        if (empty($challenge) || empty($challenge['id_voluntario'])) {
            return redirect()->to(base_url('portal/login'));
        }

        $id_voluntario = (int)$challenge['id_voluntario'];
        $voluntarioModel = new VoluntarioModel();
        $voluntario = $voluntarioModel->find($id_voluntario);

        if (!$voluntario) {
            $session->remove('otp_auth_challenge');
            return redirect()->to(base_url('portal/login'));
        }

        $telefone = (string)($challenge['telefone'] ?? '');
        $digits = preg_replace('/\D/', '', $telefone);
        $telefoneMascarado = (strlen($digits) >= 10) ? "(" . substr($digits, 0, 2) . ") 9****-" . substr($digits, -4) : $telefone;

        $data = [
            'title'             => 'Validação de Segurança OTP - ADVEC',
            'challenge'         => $challenge,
            'telefoneMascarado' => $telefoneMascarado,
            'erro'              => ''
        ];

        $otpInformado       = trim((string)$this->request->getPost('txtOtp'));
        $novaSenha          = trim((string)$this->request->getPost('txtNovaSenha'));
        $confirmaNovaSenha  = trim((string)$this->request->getPost('txtConfirmaSenha'));

        if (empty($otpInformado) || empty($novaSenha) || empty($confirmaNovaSenha)) {
            $data['erro'] = 'Por favor, preencha o código OTP e todos os campos de senha.';
            return view('portal_voluntario/trocar-senha-otp', $data);
        }

        // Validação do Código OTP e Expiração
        if (!$voluntarioModel->validarOtpTrocaSenha($id_voluntario, $otpInformado)) {
            $data['erro'] = 'Código OTP incorreto ou expirado. Verifique a mensagem no seu WhatsApp ou solicite o reenvio.';
            return view('portal_voluntario/trocar-senha-otp', $data);
        }

        // Validação da Nova Senha
        if (strlen($novaSenha) < 6) {
            $data['erro'] = 'A nova senha deve ter no mínimo 6 caracteres.';
            return view('portal_voluntario/trocar-senha-otp', $data);
        }

        if ($novaSenha !== $confirmaNovaSenha) {
            $data['erro'] = 'A confirmação de senha não coincide com a nova senha digitada.';
            return view('portal_voluntario/trocar-senha-otp', $data);
        }

        // Não permite que a nova senha seja igual ao telefone limpo (senha padrão)
        $telLimpo = preg_replace('/\D/', '', (string)$voluntario->telefone_whatsapp);
        if ($novaSenha === $telLimpo) {
            $data['erro'] = 'Sua nova senha deve ser diferente da senha padrão inicial (seu telefone).';
            return view('portal_voluntario/trocar-senha-otp', $data);
        }

        // Conclui a alteração de senha e limpa as flags de OTP
        $voluntarioModel->concluirTrocaSenhaComOtp($id_voluntario, $novaSenha);

        // Remove o desafio temporário da sessão
        $session->remove('otp_auth_challenge');

        // Cria a sessão definitiva autenticada do voluntário
        $sessionData = [
            'id_voluntario' => $voluntario->id_voluntario,
            'nome'          => $voluntario->nome,
            'nickname'      => $voluntario->nickname,
            'email'         => $voluntario->email,
            'telefone'      => $voluntario->telefone_whatsapp,
            'foto_url'      => $voluntario->foto_url,
            'logged_in'     => true,
            'role'          => 'VOLUNTARIO'
        ];

        $session->set('dsh_voluntario', $sessionData);
        session()->setFlashdata('success', 'Sua senha pessoal foi definida com sucesso! Bem-vindo(a) ao Portal do Voluntário.');

        return redirect()->to(base_url('portal/agenda'));
    }

    /**
     * AJAX: Reenvia novo código OTP por WhatsApp para o voluntário
     */
    public function reenviarOtp()
    {
        $session = session();
        $challenge = $session->get('otp_auth_challenge');

        if (empty($challenge) || empty($challenge['id_voluntario'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sessão expirada. Faça login novamente.']);
        }

        $id_voluntario = (int)$challenge['id_voluntario'];
        $voluntarioModel = new VoluntarioModel();
        $voluntario = $voluntarioModel->find($id_voluntario);

        if (!$voluntario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Voluntário não encontrado.']);
        }

        // Gera novo OTP com validade renovada
        $novoOtp = $voluntarioModel->gerarOtpTrocaSenha($id_voluntario);

        // Dispara mensagem via Webhook
        $envio = WebhookService::enviarOtpTrocaSenha($voluntario->telefone_whatsapp, $novoOtp, $voluntario->nome);

        if ($envio['success']) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Novo código de segurança enviado para o seu WhatsApp com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Não foi possível disparar o WhatsApp: ' . ($envio['message'] ?? 'Falha no webhook.')
        ]);
    }

    /**
     * Logout do Voluntário
     */
    public function logout()
    {
        $session = session();
        $session->remove('dsh_voluntario');
        return redirect()->to(base_url('portal/login'));
    }

    /**
     * Minha Agenda de Escalas (Visualização restrita ao voluntário)
     */
    public function agenda($ano = null, $mes = null)
    {
        $voluntario = $this->getVoluntarioSessao();
        if (!$voluntario) {
            return redirect()->to(base_url('portal/login'));
        }

        $ano = (int)($ano ?: date('Y'));
        $mes = (int)($mes ?: date('m'));
        if ($mes < 1) $mes = 1;
        if ($mes > 12) $mes = 12;

        $escalaModel = new EscalaVoluntarioModel();
        // Sincroniza omissões passadas
        $escalaModel->marcarOmissoesPassadas();

        $escalas = $escalaModel->getEscalasDoVoluntario($voluntario->id_voluntario, $ano, $mes);
        $participantesMap = $escalaModel->getParticipantesEscalasEmLote($escalas);

        // Agrupamento e contadores
        $totalMes      = count($escalas);
        $totalConfirmadas = 0;
        $totalRecusadas   = 0;
        $totalPendentes   = 0;

        $scheduleResourceModel = new \App\Models\ScheduleResourceModel();
        foreach ($escalas as &$esc) {
            $conf = strtoupper((string)($esc->status_confirmacao ?: 'PENDENTE'));
            if ($conf === 'CONFIRMADO') {
                $totalConfirmadas++;
            } elseif ($conf === 'RECUSADO') {
                $totalRecusadas++;
            } else {
                $totalPendentes++;
            }

            // Participantes da mesma escala/culto/departamento (excluindo o próprio voluntário logado)
            $chave = $esc->data_culto . '_' . (int)($esc->id_culto_padrao ?? 0) . '_' . (int)$esc->id_departamento;
            $todosDoCulto = $participantesMap[$chave] ?? [];
            $esc->participantes = array_values(array_filter($todosDoCulto, function($p) use ($voluntario) {
                return (int)$p->id_voluntario !== (int)$voluntario->id_voluntario;
            }));

            // Carrega lista de materiais anexados para este culto e departamento (Geral + Sub-área em que o voluntário atua)
            $esc->recursos = $scheduleResourceModel->getRecursosDoCulto(
                $esc->data_culto,
                (int)($esc->id_culto_padrao ?? 0),
                (int)$esc->id_departamento,
                (int)($esc->id_area ?? 0)
            );
        }

        $mesesNomes = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];

        $data = [
            'title'             => 'Minha Agenda - Portal do Voluntário',
            'voluntario'        => $voluntario,
            'escalas'           => $escalas,
            'ano'               => $ano,
            'mes'               => $mes,
            'nomeMes'           => $mesesNomes[$mes] ?? '',
            'totalMes'          => $totalMes,
            'totalConfirmadas'  => $totalConfirmadas,
            'totalRecusadas'    => $totalRecusadas,
            'totalPendentes'    => $totalPendentes,
            'menuAtivo'         => 'agenda'
        ];

        return view('portal_voluntario/agenda', $data);
    }

    /**
     * AJAX: Confirmação de Presença na Escala
     */
    public function confirmarEscala()
    {
        $voluntario = $this->getVoluntarioSessao();
        if (!$voluntario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sessão expirada. Faça login novamente.']);
        }

        $id_escala_voluntario = (int)$this->request->getPost('id_escala_voluntario');
        if ($id_escala_voluntario <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Escala inválida.']);
        }

        $escalaModel = new EscalaVoluntarioModel();
        $escala = $escalaModel->find($id_escala_voluntario);

        if (!$escala || (int)$escala->id_voluntario !== (int)$voluntario->id_voluntario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Escala não encontrada.']);
        }

        // Bloqueio de ação tardia se a data do evento já passou
        if (!empty($escala->data_culto) && $escala->data_culto < date('Y-m-d')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'O prazo para confirmação desta escala expirou, pois a data do evento já passou.'
            ]);
        }

        $ok = $escalaModel->responderEscala($id_escala_voluntario, $voluntario->id_voluntario, 'CONFIRMADO');

        if ($ok) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Presença confirmada com sucesso! Que Deus abençoe seu serviço.'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Não foi possível confirmar esta escala.']);
    }

    /**
     * AJAX: Recusa / Desmarcação de Presença na Escala com Justificativa
     */
    public function recusarEscala()
    {
        $voluntario = $this->getVoluntarioSessao();
        if (!$voluntario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sessão expirada. Faça login novamente.']);
        }

        $id_escala_voluntario = (int)$this->request->getPost('id_escala_voluntario');
        $justificativa        = trim((string)$this->request->getPost('justificativa'));

        if ($id_escala_voluntario <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Escala inválida.']);
        }

        if (empty($justificativa)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Por favor, informe o motivo ou justificativa para desmarcar.']);
        }

        $escalaModel = new EscalaVoluntarioModel();
        $escala = $escalaModel->find($id_escala_voluntario);

        if (!$escala || (int)$escala->id_voluntario !== (int)$voluntario->id_voluntario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Escala não encontrada.']);
        }

        // Bloqueio de ação tardia se a data do evento já passou
        if (!empty($escala->data_culto) && $escala->data_culto < date('Y-m-d')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'O prazo para desmarcação desta escala expirou, pois a data do evento já passou.'
            ]);
        }

        $ok = $escalaModel->responderEscala($id_escala_voluntario, $voluntario->id_voluntario, 'RECUSADO', $justificativa);

        if ($ok) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Escala desmarcada com sucesso. Sua liderança foi informada.'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Não foi possível desmarcar esta escala.']);
    }

    /**
     * Tela de Métricas, Assiduidade & Ranking dos Voluntários por Departamento (Gamificação Justa)
     */
    public function metricas()
    {
        $voluntario = $this->getVoluntarioSessao();
        if (!$voluntario) {
            return redirect()->to(base_url('portal/login'));
        }

        $periodo = $this->request->getGet('periodo') ?: 'mes_atual';
        $id_dep_param = $this->request->getGet('id_departamento');

        $voluntarioModel = new VoluntarioModel();
        $meusDepartamentos = $voluntarioModel->getDepartamentosDoVoluntario($voluntario->id_voluntario);

        // Se veio explicitamente 'todos', define como null (visão geral consolidada)
        if ($id_dep_param === 'todos') {
            $id_departamento_selecionado = null;
        } elseif ($id_dep_param !== null && $id_dep_param !== '' && is_numeric($id_dep_param)) {
            $id_departamento_selecionado = (int)$id_dep_param;
        } else {
            // Padrão: Sempre abrir no primeiro departamento do voluntário para separar as métricas justamente
            if (!empty($meusDepartamentos)) {
                $id_departamento_selecionado = (int)$meusDepartamentos[0]->id_departamento;
            } else {
                $id_departamento_selecionado = null;
            }
        }

        // Recupera dados do departamento selecionado se houver
        $departamentoAtual = null;
        if ($id_departamento_selecionado) {
            foreach ($meusDepartamentos as $dep) {
                if ((int)$dep->id_departamento === $id_departamento_selecionado) {
                    $departamentoAtual = $dep;
                    break;
                }
            }
            if (!$departamentoAtual) {
                $depModel = new \App\Models\DepartamentoModel();
                $departamentoAtual = $depModel->find($id_departamento_selecionado);
            }
        }

        $metricas    = $voluntarioModel->getEstatisticasVoluntario($voluntario->id_voluntario, $periodo, $id_departamento_selecionado);
        $rankingData = $voluntarioModel->getRankingVoluntariosPortal($voluntario->id_voluntario, $periodo, $id_departamento_selecionado);

        $data = [
            'title'                       => 'Métricas & Ranking - Portal do Voluntário',
            'voluntario'                  => $voluntario,
            'meusDepartamentos'           => $meusDepartamentos,
            'id_departamento_selecionado' => $id_departamento_selecionado,
            'departamentoAtual'           => $departamentoAtual,
            'metricas'                    => $metricas,
            'rankingData'                 => $rankingData,
            'periodo'                     => $periodo,
            'menuAtivo'                   => 'metricas'
        ];

        return view('portal_voluntario/metricas', $data);
    }

    /**
     * Tela de Edição de Perfil do Voluntário (Acesso ao clicar no nome/foto)
     */
     public function perfil()
     {
         $voluntario = $this->getVoluntarioSessao();
         if (!$voluntario) {
             return redirect()->to(base_url('portal/login'));
         }

         // Redes Sociais Decodificadas
         $redesSociais = [];
         if (!empty($voluntario->redes_sociais)) {
             $dec = json_decode($voluntario->redes_sociais, true);
             if (is_array($dec)) {
                 $redesSociais = $dec;
             }
         }

         $cultoPadraoModel     = new CultoPadraoModel();
         $voluntarioCultoModel = new VoluntarioCultoModel();

         $data = [
             'title'                => 'Meu Perfil - Portal do Voluntário',
             'voluntario'           => $voluntario,
             'redesSociais'         => $redesSociais,
             'cultosPadrao'         => $cultoPadraoModel->getCultosPadraoAtivos(),
             'cultosSelecionadosIds'=> $voluntarioCultoModel->getIdsCultosDoVoluntario($voluntario->id_voluntario),
             'menuAtivo'            => 'perfil'
         ];

         return view('portal_voluntario/perfil', $data);
     }

    /**
     * AJAX: Salva dados permitidos do perfil do voluntário
     */
    public function salvarPerfil()
    {
        $voluntario = $this->getVoluntarioSessao();
        if (!$voluntario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sessão expirada. Faça login novamente.']);
        }

        try {
            $nickname         = trim((string)$this->request->getPost('nickname'));
            $data_nascimento  = trim((string)$this->request->getPost('data_nascimento'));
            $telefone_whatsapp= trim((string)$this->request->getPost('telefone_whatsapp'));
            $redesPlataformas = $this->request->getPost('rede_plataforma') ?: [];
            $redesUrls        = $this->request->getPost('rede_url') ?: [];

            // Se o telefone não foi enviado no formulário, mantém o telefone cadastrado
            if (empty($telefone_whatsapp)) {
                $telefone_whatsapp = (string)($voluntario->telefone_whatsapp ?? '');
            }

            if (empty($telefone_whatsapp)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'O telefone WhatsApp é obrigatório.']);
            }

            // Processa Redes Sociais
            $redesArray = [];
            if (is_array($redesPlataformas) && is_array($redesUrls)) {
                foreach ($redesPlataformas as $idx => $plat) {
                    $u = trim((string)($redesUrls[$idx] ?? ''));
                    if (!empty($plat) && !empty($u)) {
                        $redesArray[] = [
                            'plataforma' => trim($plat),
                            'url'        => $u,
                            'rede'       => trim($plat),
                            'link'       => $u
                        ];
                    }
                }
            }
            $redesJson = !empty($redesArray) ? json_encode($redesArray, JSON_UNESCAPED_UNICODE) : null;

            $dadosUpdate = [
                'nickname'          => $nickname ?: ($voluntario->nickname ?? null),
                'data_nascimento'   => !empty($data_nascimento) ? $data_nascimento : ($voluntario->data_nascimento ?? null),
                'telefone_whatsapp' => $telefone_whatsapp,
                'redes_sociais'     => $redesJson
            ];

            // Upload de Foto (se enviada junto com o form)
            $fotoFile = $this->request->getFile('foto_file');
            if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
                $uploadDir = FCPATH . 'uploads/voluntarios';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                $newName = $fotoFile->getRandomName();
                $fotoFile->move($uploadDir, $newName);
                $dadosUpdate['foto_url'] = base_url('uploads/voluntarios/' . $newName);
            }

            $voluntarioModel = new VoluntarioModel();
            $voluntarioModel->atualizarPerfilVoluntario($voluntario->id_voluntario, $dadosUpdate);

            // Sincroniza Disponibilidade de Cultos (N:N) se o campo estiver presente
            $cultosPost = $this->request->getPost('cultos');
            $cultosIds = is_array($cultosPost) ? $cultosPost : [];
            $voluntarioCultoModel = new VoluntarioCultoModel();
            $voluntarioCultoModel->sincronizarCultos($voluntario->id_voluntario, (array)$cultosIds);

            // Atualiza sessão
            $sess = session();
            $currSess = $sess->get('dsh_voluntario');
            $currSess['nickname'] = $dadosUpdate['nickname'];
            $currSess['telefone'] = $telefone_whatsapp;
            if (!empty($dadosUpdate['foto_url'])) {
                $currSess['foto_url'] = $dadosUpdate['foto_url'];
            }
            $sess->set('dsh_voluntario', $currSess);

            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => 'Seu perfil foi atualizado com sucesso!',
                'foto_url' => $dadosUpdate['foto_url'] ?? $voluntario->foto_url
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao salvar perfil: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Upload Direto de Foto de Perfil
     */
    public function uploadFoto()
    {
        $voluntario = $this->getVoluntarioSessao();
        if (!$voluntario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sessão expirada. Faça login novamente.']);
        }

        $fotoFile = $this->request->getFile('foto_file');
        if (!$fotoFile || !$fotoFile->isValid() || $fotoFile->hasMoved()) {
            $msgErro = ($fotoFile && !$fotoFile->isValid()) ? $fotoFile->getErrorString() : 'Arquivo de imagem inválido ou não enviado.';
            return $this->response->setJSON(['status' => 'error', 'message' => $msgErro]);
        }

        try {
            $uploadDir = FCPATH . 'uploads/voluntarios';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $newName = $fotoFile->getRandomName();
            $fotoFile->move($uploadDir, $newName);
            $fotoUrl = base_url('uploads/voluntarios/' . $newName);

            $voluntarioModel = new VoluntarioModel();
            $voluntarioModel->atualizarPerfilVoluntario($voluntario->id_voluntario, ['foto_url' => $fotoUrl]);

            // Atualiza sessão
            $sess = session();
            $currSess = $sess->get('dsh_voluntario');
            $currSess['foto_url'] = $fotoUrl;
            $sess->set('dsh_voluntario', $currSess);

            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => 'Sua foto foi atualizada com sucesso!',
                'foto_url' => $fotoUrl
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao salvar foto: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Alteração de Senha pelo Voluntário
     */
    public function alterarSenha()
    {
        $voluntario = $this->getVoluntarioSessao();
        if (!$voluntario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sessão expirada. Faça login novamente.']);
        }

        $senha_atual        = trim((string)$this->request->getPost('senha_atual'));
        $nova_senha         = trim((string)$this->request->getPost('nova_senha'));
        $confirma_nova_senha= trim((string)$this->request->getPost('confirma_nova_senha'));

        if (empty($senha_atual) || empty($nova_senha) || empty($confirma_nova_senha)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Preencha todos os campos de senha.']);
        }

        if (strlen($nova_senha) < 6) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'A nova senha deve ter no mínimo 6 caracteres.']);
        }

        if ($nova_senha !== $confirma_nova_senha) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'A confirmação de senha não coincide com a nova senha.']);
        }

        // Valida senha atual
        $digitosTelefone = preg_replace('/\D/', '', (string)$voluntario->telefone_whatsapp);
        $digitosTelefoneSem55 = (strpos($digitosTelefone, '55') === 0 && strlen($digitosTelefone) >= 12) ? substr($digitosTelefone, 2) : $digitosTelefone;

        $senhaAtualCorreta = false;
        if (!empty($voluntario->senha)) {
            if (password_verify($senha_atual, $voluntario->senha)) {
                $senhaAtualCorreta = true;
            }
        } else {
            if ($senha_atual === $digitosTelefone || $senha_atual === $digitosTelefoneSem55 || $senha_atual === $voluntario->telefone_whatsapp) {
                $senhaAtualCorreta = true;
            }
        }

        if (!$senhaAtualCorreta) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'A senha atual informada está incorreta.']);
        }

        $voluntarioModel = new VoluntarioModel();
        $voluntarioModel->alterarSenha($voluntario->id_voluntario, $nova_senha);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Sua senha foi alterada com sucesso!'
        ]);
    }
}
