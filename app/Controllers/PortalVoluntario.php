<?php

namespace App\Controllers;

use App\Models\VoluntarioModel;
use App\Models\VoluntarioAreaModel;
use App\Models\VoluntarioCultoModel;
use App\Models\CultoPadraoModel;
use App\Models\EscalaVoluntarioModel;

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
        if ($vol) {
            $voluntarioAreaModel = new VoluntarioAreaModel();
            $vol->areas = $voluntarioAreaModel->getAreasDoVoluntario($vol->id_voluntario);
        }
        return $vol;
    }

    /**
     * Tela e Processamento de Login do Voluntário
     */
    public function login()
    {
        $session = session();
        if ($session->has('dsh_voluntario') && !empty($session->get('dsh_voluntario')['logged_in'])) {
            return redirect()->to(base_url('portal/agenda'));
        }

        $data = [
            'title'     => 'Acesso do Voluntário - ADVEC',
            'telefone'  => '',
            'erro'      => ''
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

            if ($voluntario->status != 1) {
                $data['erro'] = 'Seu cadastro de voluntário está inativo. Fale com o líder do seu departamento.';
                return view('portal_voluntario/login', $data);
            }

            // Validação de senha:
            // 1. Se tem hash no banco, valida via password_verify
            // 2. Se a senha está nula ou coincide com a senha padrão (dígitos do telefone)
            $digitosTelefone = preg_replace('/\D/', '', (string)$voluntario->telefone_whatsapp);
            $digitosTelefoneSem55 = (strpos($digitosTelefone, '55') === 0 && strlen($digitosTelefone) >= 12) ? substr($digitosTelefone, 2) : $digitosTelefone;

            $senhaValida = false;

            if (!empty($voluntario->senha)) {
                if (password_verify($senha, $voluntario->senha)) {
                    $senhaValida = true;
                } elseif ($senha === $digitosTelefone || $senha === $digitosTelefoneSem55) {
                    // Senha padrão informada pelo usuário
                    $senhaValida = true;
                }
            } else {
                // Sem hash gravado ainda: senha padrão é o telefone limpo
                if ($senha === $digitosTelefone || $senha === $digitosTelefoneSem55 || $senha === $voluntario->telefone_whatsapp) {
                    $senhaValida = true;
                    // Gera hash para futuros logins
                    $voluntarioModel->update($voluntario->id_voluntario, [
                        'senha'             => password_hash($senha, PASSWORD_BCRYPT),
                        'primeiro_acesso'   => 1,
                        'data_ultima_senha' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            if (!$senhaValida) {
                $data['erro'] = 'Senha incorreta. Caso seja seu primeiro acesso, sua senha inicial é o seu número de WhatsApp.';
                return view('portal_voluntario/login', $data);
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
        $escalas = $escalaModel->getEscalasDoVoluntario($voluntario->id_voluntario, $ano, $mes);

        // Agrupamento e contadores
        $totalMes      = count($escalas);
        $totalConfirmadas = 0;
        $totalRecusadas   = 0;
        $totalPendentes   = 0;

        foreach ($escalas as $esc) {
            $conf = strtoupper((string)$esc->status_confirmacao);
            if ($conf === 'CONFIRMADO') {
                $totalConfirmadas++;
            } elseif ($conf === 'RECUSADO') {
                $totalRecusadas++;
            } else {
                $totalPendentes++;
            }
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
     * Tela de Métricas, Assiduidade & Ranking dos Voluntários (Gamificação Premium)
     */
    public function metricas()
    {
        $voluntario = $this->getVoluntarioSessao();
        if (!$voluntario) {
            return redirect()->to(base_url('portal/login'));
        }

        $periodo = $this->request->getGet('periodo') ?: 'mes_atual';

        $voluntarioModel = new VoluntarioModel();
        $metricas        = $voluntarioModel->getEstatisticasVoluntario($voluntario->id_voluntario, $periodo);
        $rankingData     = $voluntarioModel->getRankingVoluntariosPortal($voluntario->id_voluntario, $periodo);

        $data = [
            'title'        => 'Métricas & Ranking - Portal do Voluntário',
            'voluntario'   => $voluntario,
            'metricas'     => $metricas,
            'rankingData'  => $rankingData,
            'periodo'      => $periodo,
            'menuAtivo'    => 'metricas'
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
                            'url'        => $u
                        ];
                    }
                }
            }
            $redesJson = !empty($redesArray) ? json_encode($redesArray, JSON_UNESCAPED_UNICODE) : null;

            $dadosUpdate = [
                'nickname'          => $nickname,
                'data_nascimento'   => $data_nascimento,
                'telefone_whatsapp' => $telefone_whatsapp,
                'redes_sociais'     => $redesJson
            ];

            // Upload de Foto
            $fotoFile = $this->request->getFile('foto_file');
            if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
                $ext = strtolower($fotoFile->getClientExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $newName = 'vol_' . $voluntario->id_voluntario . '_' . time() . '.' . $ext;
                    $uploadPath = ROOTPATH . 'public/uploads/voluntarios/';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $fotoFile->move($uploadPath, $newName);
                    $dadosUpdate['foto_url'] = base_url('uploads/voluntarios/' . $newName);
                }
            }

            $voluntarioModel = new VoluntarioModel();
            $voluntarioModel->atualizarPerfilVoluntario($voluntario->id_voluntario, $dadosUpdate);

            // Sincroniza Disponibilidade de Cultos (N:N)
            $cultosIds = $this->request->getPost('cultos') ?: [];
            $voluntarioCultoModel = new VoluntarioCultoModel();
            $voluntarioCultoModel->sincronizarCultos($voluntario->id_voluntario, (array)$cultosIds);

            // Atualiza sessão
            $sess = session();
            $currSess = $sess->get('dsh_voluntario');
            $currSess['nickname'] = $nickname;
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
            if (password_verify($senha_atual, $voluntario->senha) || $senha_atual === $digitosTelefone || $senha_atual === $digitosTelefoneSem55) {
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
