<?php

namespace App\Controllers;

use App\Models\DepartamentoConviteModel;
use App\Models\DepartamentoModel;
use App\Models\DepartamentoAreaModel;
use App\Models\CultoPadraoModel;
use App\Models\VoluntarioCultoModel;
use App\Models\VoluntarioModel;
use App\Models\VoluntarioAreaModel;
use App\Services\WebhookService;

class Convite extends BaseController
{
    /**
     * Landing Page do Convite de Self-Onboarding (Mobile-First)
     * GET /convite/(:any)
     */
    public function index($token)
    {
        $token = trim((string)$token);

        // Validação estrita anti-SQL injection / formato seguro de token
        if (!preg_match('/^[a-f0-9]{16,64}$/i', $token)) {
            return view('convite/invalido', [
                'motivo'  => 'Formato de link de convite inválido ou adulterado.',
                'convite' => null,
                'token'   => $token
            ]);
        }

        $conviteModel = new DepartamentoConviteModel();
        $validacao = $conviteModel->validarToken($token);

        if (!$validacao['valido']) {
            return view('convite/invalido', [
                'motivo'  => $validacao['motivo'],
                'convite' => $validacao['convite'],
                'token'   => $token
            ]);
        }

        $convite = $validacao['convite'];
        $areaModel = new DepartamentoAreaModel();
        $subareas = $areaModel->getAreasPorDepartamento($convite->id_departamento, true);

        // Carrega cultos padrão para configuração de disponibilidade
        $cultoPadraoModel = new CultoPadraoModel();
        $cultosPadrao = $cultoPadraoModel->getCultosPadraoAtivos();

        // Prepara dados do desafio caso já tenha em sessão
        $session = session();
        $challenge = $session->get('convite_challenge_' . $token);

        $data = [
            'convite'      => $convite,
            'subareas'     => $subareas,
            'cultosPadrao' => $cultosPadrao,
            'token'        => $token,
            'challenge'    => $challenge
        ];

        return view('convite/landing', $data);
    }

    /**
     * Dispara código OTP de 6 dígitos via WhatsApp para validação do número
     * POST /convite/solicitar-otp
     */
    public function solicitarOtp()
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Método inválido.']);
        }

        $token    = trim((string)$this->request->getPost('token'));
        $telefone = trim((string)$this->request->getPost('telefone'));

        if (empty($token) || empty($telefone)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Token do convite e telefone WhatsApp são obrigatórios.'
            ]);
        }

        $conviteModel = new DepartamentoConviteModel();
        $validacao = $conviteModel->validarToken($token);

        if (!$validacao['valido']) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => $validacao['motivo']
            ]);
        }

        $convite = $validacao['convite'];
        $digitosTelefone = preg_replace('/\D/', '', $telefone);

        // Se for Convite DIRETO, o telefone DEVE coincidir estritamente com o convidado
        if ($convite->tipo === 'DIRETO' && !empty($convite->telefone)) {
            $digitosConvite = preg_replace('/\D/', '', $convite->telefone);
            
            // Compara com e sem 55
            $tel1 = (strpos($digitosTelefone, '55') === 0 && strlen($digitosTelefone) >= 12) ? substr($digitosTelefone, 2) : $digitosTelefone;
            $tel2 = (strpos($digitosConvite, '55') === 0 && strlen($digitosConvite) >= 12) ? substr($digitosConvite, 2) : $digitosConvite;

            if ($tel1 !== $tel2) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Este convite direto é exclusivo para o número de WhatsApp convidado.'
                ]);
            }
        }

        // Trava de Segurança: Valida se o celular já possui cadastro como voluntário
        $voluntarioModel = new VoluntarioModel();
        if ($voluntarioModel->existeTelefone($telefone)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Este número de WhatsApp já possui cadastro como voluntário no sistema. Acesse o Portal do Voluntário para gerenciar suas escalas e perfil.'
            ]);
        }

        // Gera código OTP de 6 dígitos com validade de 10 minutos
        $otpCode = sprintf('%06d', mt_rand(100000, 999999));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Salva desafio temporário na sessão
        $session = session();
        $session->set('convite_challenge_' . $token, [
            'token'           => $token,
            'id_convite'      => (int)$convite->id_convite,
            'id_departamento' => (int)$convite->id_departamento,
            'telefone'        => $telefone,
            'otp_code'        => $otpCode,
            'otp_expires_at'  => $expiresAt,
            'validado'        => false
        ]);

        // Dispara mensagem via WhatsApp
        $nomeDep = !empty($convite->nome_departamento) ? $convite->nome_departamento : 'ADVEC';
        $msgWhatsApp = "ADVEC: Olá! Seu código de verificação para o cadastro no departamento *{$nomeDep}* é: *{$otpCode}*. Este código expira em 10 minutos.";

        $envio = WebhookService::dispararMensagemWhatsApp($telefone, $msgWhatsApp);

        if ($envio['success']) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Código de verificação enviado para o seu WhatsApp!'
            ]);
        }

        return $this->response->setJSON([
            'status'     => 'success', // Retorna success para prosseguir em ambiente de teste, com alerta de mensagem
            'simulado'   => true,
            'otp_debug'  => $otpCode,
            'message'    => 'Código gerado! (WhatsApp offline ou webhook pendente). Código para teste: ' . $otpCode
        ]);
    }

    /**
     * Valida o código OTP informado pelo voluntário
     * POST /convite/validar-otp
     */
    public function validarOtp()
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Método inválido.']);
        }

        $token   = trim((string)$this->request->getPost('token'));
        $otpCode = trim((string)$this->request->getPost('otp'));

        $session = session();
        $challenge = $session->get('convite_challenge_' . $token);

        if (!$challenge || empty($challenge['otp_code'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Nenhuma solicitação de verificação ativa. Solicite um novo código.'
            ]);
        }

        if (strtotime($challenge['otp_expires_at']) < time()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'O código de verificação expirou. Solicite um novo código.'
            ]);
        }

        if ($challenge['otp_code'] !== $otpCode) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Código de verificação incorreto. Verifique o número digitado.'
            ]);
        }

        // Marca como validado
        $challenge['validado'] = true;
        $session->set('convite_challenge_' . $token, $challenge);

        return $this->response->setJSON([
            'status'   => 'success',
            'telefone' => $challenge['telefone'],
            'message'  => 'WhatsApp verificado com sucesso!'
        ]);
    }

    /**
     * Conclui o pré-cadastro do voluntário (Self-Onboarding)
     * POST /convite/concluir-cadastro
     */
    public function concluirCadastro()
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Método inválido.']);
        }

        $token = trim((string)$this->request->getPost('token'));
        if (!preg_match('/^[a-f0-9]{16,64}$/i', $token)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Token de convite inválido.'
            ]);
        }

        $session = session();
        $challenge = $session->get('convite_challenge_' . $token);

        if (!$challenge || empty($challenge['validado'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Você precisa validar seu WhatsApp via código OTP antes de finalizar o cadastro.'
            ]);
        }

        $conviteModel = new DepartamentoConviteModel();
        $validacao = $conviteModel->validarToken($token);

        if (!$validacao['valido']) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => $validacao['motivo']
            ]);
        }

        $convite = $validacao['convite'];

        $nome            = trim((string)$this->request->getPost('nome'));
        $nickname        = trim((string)$this->request->getPost('nickname')) ?: null;
        $data_nascimento = trim((string)$this->request->getPost('data_nascimento'));
        $idAreasPost     = $this->request->getPost('id_area');
        if (!is_array($idAreasPost)) {
            $idAreasPost = !empty($idAreasPost) ? [$idAreasPost] : [];
        }
        $idAreas = array_values(array_unique(array_filter(array_map('intval', $idAreasPost))));

        if (empty($nome) || empty($data_nascimento) || empty($idAreas)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Por favor, preencha todos os campos obrigatórios (Nome, Data de Nascimento e pelo menos uma Sub-área).'
            ]);
        }

        // Valida se as sub-áreas pertencem ao departamento do convite
        $areaModel = new DepartamentoAreaModel();
        $areasValidas = $areaModel->whereIn('id_area', $idAreas)
            ->where('id_departamento', (int)$convite->id_departamento)
            ->findAll();

        if (empty($areasValidas)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Sub-área(s) de atuação inválida(s) para este departamento.'
            ]);
        }

        $idAreaPrimaria = (int)$idAreas[0];

        $telefone = $challenge['telefone'];

        // Trava de Segurança adicional: garante que o telefone não foi cadastrado concorrentemente
        $voluntarioModel = new VoluntarioModel();
        if ($voluntarioModel->existeTelefone($telefone)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Este número de telefone já está cadastrado no sistema.'
            ]);
        }

        $email = trim((string)$this->request->getPost('email'));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'O formato do e-mail informado é inválido. Deixe em branco caso não deseje cadastrar e-mail.'
            ]);
        }

        // Validação obrigatória de ao menos 1 culto de disponibilidade
        $cultosSelecionados = $this->request->getPost('cultos') ?: [];
        if (empty($cultosSelecionados) || !is_array($cultosSelecionados)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Por favor, selecione ao menos um dia/culto em sua disponibilidade de servir.'
            ]);
        }

        // Upload de foto caso enviada
        $foto_url = null;
        $foto_url_manual = trim((string)$this->request->getPost('foto_url_manual'));
        if (!empty($foto_url_manual)) {
            $foto_url = $foto_url_manual;
        }

        $fileFoto = $this->request->getFile('foto_file');
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/voluntarios';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $newName = $fileFoto->getRandomName();
            $fileFoto->move($uploadDir, $newName);
            $foto_url = base_url('uploads/voluntarios/' . $newName);
        }

        // Redes Sociais caso informadas (suporta múltiplos formatos de input e json)
        $redesNomes = $this->request->getPost('rede_plataforma') ?: $this->request->getPost('rede_nome') ?: [];
        $redesUrls  = $this->request->getPost('rede_url') ?: $this->request->getPost('rede_link') ?: [];
        $redesArray = [];
        if (is_array($redesNomes) && is_array($redesUrls)) {
            foreach ($redesNomes as $idx => $rNome) {
                $rUrl = trim((string)($redesUrls[$idx] ?? ''));
                $rNome = trim((string)$rNome);
                if (!empty($rNome) && !empty($rUrl)) {
                    $redesArray[] = [
                        'rede'       => $rNome,
                        'link'       => $rUrl,
                        'plataforma' => $rNome,
                        'url'        => $rUrl
                    ];
                }
            }
        }

        if (empty($redesArray)) {
            $rawRedes = $this->request->getPost('redes_sociais');
            if (!empty($rawRedes)) {
                $decoded = is_string($rawRedes) ? json_decode($rawRedes, true) : $rawRedes;
                if (is_array($decoded)) {
                    foreach ($decoded as $item) {
                        $pName = trim((string)($item['plataforma'] ?? $item['rede'] ?? ''));
                        $pLink = trim((string)($item['url'] ?? $item['link'] ?? ''));
                        if (!empty($pName) && !empty($pLink)) {
                            $redesArray[] = [
                                'rede'       => $pName,
                                'link'       => $pLink,
                                'plataforma' => $pName,
                                'url'        => $pLink
                            ];
                        }
                    }
                }
            }
        }

        $voluntarioModel = new VoluntarioModel();
        $voluntarioExistente = $voluntarioModel->buscarPorTelefone($telefone);

        if ($voluntarioExistente) {
            $idVoluntario = (int)$voluntarioExistente->id_voluntario;
            
            // Atualiza dados cadastrais fornecidos se pendente ou complementa
            $dadosUpdate = [
                'status_aprovacao'   => 'PENDENTE',
                'id_convite_origem'  => (int)$convite->id_convite,
                'email'              => $email
            ];
            if ($nickname) $dadosUpdate['nickname'] = $nickname;
            if ($foto_url) $dadosUpdate['foto_url'] = $foto_url;
            if (!empty($redesArray)) $dadosUpdate['redes_sociais'] = json_encode($redesArray, JSON_UNESCAPED_UNICODE);

            $voluntarioModel->update($idVoluntario, $dadosUpdate);
        } else {
            // Cria novo voluntário com status pendente de aprovação do líder
            $digitosTelefone = preg_replace('/\D/', '', $telefone);
            $hashSenha = password_hash($digitosTelefone, PASSWORD_BCRYPT);

            $novoVoluntario = [
                'id_filial'          => 1,
                'nome'               => $nome,
                'nickname'           => $nickname,
                'nivel_conhecimento' => 'JUNIOR',
                'email'              => $email,
                'senha'              => $hashSenha,
                'primeiro_acesso'    => 1,
                'force_pwd_change'   => 1,
                'telefone_whatsapp'  => $telefone,
                'data_nascimento'    => $data_nascimento,
                'foto_url'           => $foto_url,
                'status'             => 0, // Inativo até aprovação do líder
                'status_aprovacao'   => 'PENDENTE',
                'id_convite_origem'  => (int)$convite->id_convite,
                'max_escalas_mes'    => 0,
                'redes_sociais'      => !empty($redesArray) ? json_encode($redesArray, JSON_UNESCAPED_UNICODE) : null,
                'observacao'         => "Cadastro realizado via Self-Onboarding (Convite: {$token})"
            ];

            $idVoluntario = $voluntarioModel->insert($novoVoluntario);
            $hash = sha1('VOL_' . $idVoluntario . '_' . time());
            $voluntarioModel->update($idVoluntario, ['hash_voluntario' => $hash]);
        }

        // Vincula todas as sub-áreas solicitadas ao voluntário na tabela relacional
        $volAreaModel = new VoluntarioAreaModel();
        $volAreaModel->sincronizarAreas($idVoluntario, $idAreas, [(int)$convite->id_departamento]);

        // Sincroniza Disponibilidade de Cultos (N:N)
        $voluntarioCultoModel = new VoluntarioCultoModel();
        $voluntarioCultoModel->sincronizarCultos($idVoluntario, (array)$cultosSelecionados);

        // Incrementa contagem de uso do convite
        $conviteModel->incrementarUso($convite->id_convite);

        // Limpa desafio da sessão
        $session->remove('convite_challenge_' . $token);

        return $this->response->setJSON([
            'status'   => 'success',
            'redirect' => base_url('convite/sucesso'),
            'message'  => 'Cadastro enviado com sucesso para análise!'
        ]);
    }

    /**
     * Tela de Sucesso após Envio do Self-Onboarding
     * GET /convite/sucesso
     */
    public function sucesso()
    {
        return view('convite/sucesso');
    }
}
