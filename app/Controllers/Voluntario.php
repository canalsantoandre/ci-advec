<?php

namespace App\Controllers;

use App\Models\VoluntarioModel;
use App\Models\VoluntarioAreaModel;
use App\Models\VoluntarioCultoModel;
use App\Models\CultoPadraoModel;
use App\Models\DepartamentoModel;
use App\Models\DepartamentoAreaModel;
use App\Models\DepartamentoGestorModel;
use App\Models\DepartamentoConviteModel;
use App\Models\SessionModel;
use App\Services\WebhookService;

class Voluntario extends BaseController
{
    private function session()
    {
        $data = [];
        $session = new SessionModel();
        $data = $session->retornaSessao($data, 'voluntario/');
        return $data;
    }

    /**
     * Listagem de Voluntários com busca e filtros
     */
    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $departamentoGestorModel    = new DepartamentoGestorModel();
        $departamentosPermitidos    = $departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        $filtros = [
            'id_departamento' => $this->request->getGet('id_departamento'),
            'status'          => $this->request->getGet('status'),
            'busca'           => $this->request->getGet('busca')
        ];

        if (!empty($filtros['id_departamento'])) {
            if (!in_array((int)$filtros['id_departamento'], $departamentosPermitidosIds)) {
                $filtros['id_departamento'] = !empty($departamentosPermitidosIds) ? $departamentosPermitidosIds[0] : -1;
            }
        } else {
            $filtros['departamentos_permitidos'] = $departamentosPermitidosIds;
        }

        $voluntarioModel = new VoluntarioModel();
        $data['voluntarios'] = $voluntarioModel->listaVoluntarios($filtros);

        // Carrega pendentes de aprovação e convites para o líder
        $data['abaAtiva']       = $this->request->getGet('tab') ?: 'ativos';
        $data['totalPendentes'] = $voluntarioModel->contarPendentes($departamentosPermitidosIds);
        $data['pendentes']      = $voluntarioModel->listaPendentesAprovacao($departamentosPermitidosIds, $filtros);

        $conviteModel           = new DepartamentoConviteModel();
        $data['convites']       = $conviteModel->listarConvites($departamentosPermitidosIds);

        $areaModel              = new DepartamentoAreaModel();
        $data['todasAreas']     = $areaModel->getTodasAreasAgrupadas();

        $data['departamentos']              = $departamentosPermitidos;
        $data['departamentosPermitidosIds'] = $departamentosPermitidosIds;
        $data['filtros']                    = $filtros;

        $data['content_view'] = view('voluntario/voluntario-list', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Novo Voluntário
     */
    public function novo()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $areaModel               = new DepartamentoAreaModel();
        $cultoPadraoModel        = new CultoPadraoModel();
        $departamentoGestorModel = new DepartamentoGestorModel();

        $departamentosPermitidos    = $departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $todasAreasAgrupadas        = $areaModel->getTodasAreasAgrupadas();
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        $data['departamentosPermitidos']    = $departamentosPermitidos;
        $data['departamentosPermitidosIds'] = $departamentosPermitidosIds;
        $data['departamentosComAreas']      = $todasAreasAgrupadas;
        $data['cultosPadrao']               = $cultoPadraoModel->getCultosPadraoAtivos();
        $data['cultosSelecionadosIds']      = [];
        $data['voluntario']                 = null;
        $data['areasSelecionadasIds']       = [];
        $data['redesSociais']               = [];
        $data['stats']                      = null;

        $data['content_view'] = view('voluntario/voluntario-form', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Edição de Voluntário
     */
    public function editar($id_ou_hash)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $voluntarioModel = new VoluntarioModel();
        if (is_numeric($id_ou_hash)) {
            $voluntario = $voluntarioModel->find((int)$id_ou_hash);
        } else {
            $voluntario = $voluntarioModel->findByColumn('hash_voluntario', $id_ou_hash);
        }

        if (!$voluntario) {
            session()->setFlashdata('error', 'Voluntário não encontrado.');
            return redirect()->to('voluntario');
        }

        $voluntarioAreaModel     = new VoluntarioAreaModel();
        $voluntarioCultoModel    = new VoluntarioCultoModel();
        $areaModel               = new DepartamentoAreaModel();
        $cultoPadraoModel        = new CultoPadraoModel();
        $departamentoGestorModel = new DepartamentoGestorModel();

        $departamentosPermitidos    = $departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $todasAreasAgrupadas        = $areaModel->getTodasAreasAgrupadas();
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        $data['voluntario']                 = $voluntario;
        $data['departamentosPermitidos']    = $departamentosPermitidos;
        $data['departamentosPermitidosIds'] = $departamentosPermitidosIds;
        $data['areasSelecionadasIds']       = $voluntarioAreaModel->getIdsAreasDoVoluntario($voluntario->id_voluntario);
        $data['cultosSelecionadosIds']      = $voluntarioCultoModel->getIdsCultosDoVoluntario($voluntario->id_voluntario);
        $data['cultosPadrao']               = $cultoPadraoModel->getCultosPadraoAtivos();
        $data['departamentosComAreas']      = $todasAreasAgrupadas;

        // Decodifica redes sociais se houver
        $redes = [];
        if (!empty($voluntario->redes_sociais)) {
            $decoded = json_decode($voluntario->redes_sociais, true);
            if (is_array($decoded)) {
                $redes = $decoded;
            }
        }
        $data['redesSociais'] = $redes;

        // Métricas de assiduidade
        $periodo = $this->request->getGet('periodo') ?: 'tudo';
        $data['periodoSelecionado'] = $periodo;
        $data['stats'] = $voluntarioModel->getEstatisticasVoluntario($voluntario->id_voluntario, $periodo);

        $data['content_view'] = view('voluntario/voluntario-form', $data);
        return view('_layout', $data);
    }

    /**
     * Salva ou Atualiza Voluntário
     */
    public function salvar()
    {
        $data = $this->session();
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to('voluntario');
        }

        $id_voluntario = (int)$this->request->getPost('id_voluntario');

        if ($id_voluntario > 0) {
            if (empty($data['sys_action']->update)) {
                return redirect()->to('accessdeny');
            }
        } else {
            if (empty($data['sys_action']->create)) {
                return redirect()->to('accessdeny');
            }
        }

        $nome              = trim((string)$this->request->getPost('nome'));
        $email             = trim((string)$this->request->getPost('email'));
        $telefone_whatsapp = trim((string)$this->request->getPost('telefone_whatsapp'));
        $data_nascimento   = (string)$this->request->getPost('data_nascimento');
        $status            = (int)$this->request->getPost('status');
        $observacao        = trim((string)$this->request->getPost('observacao'));
        $foto_url_manual   = trim((string)$this->request->getPost('foto_url_manual'));

        if (empty($nome) || empty($email) || empty($telefone_whatsapp) || empty($data_nascimento)) {
            session()->setFlashdata('error', 'Preencha todos os campos obrigatórios (*).');
            return redirect()->back()->withInput();
        }

        // Processa redes sociais dinâmicas
        $redesNomes = $this->request->getPost('rede_nome') ?: [];
        $redesLinks = $this->request->getPost('rede_link') ?: [];
        $redesArray = [];

        if (is_array($redesNomes) && is_array($redesLinks)) {
            foreach ($redesNomes as $idx => $redeNome) {
                $redeLink = trim($redesLinks[$idx] ?? '');
                $redeNome = trim($redeNome);
                if (!empty($redeNome) && !empty($redeLink)) {
                    $redesArray[] = [
                        'rede'       => $redeNome,
                        'link'       => $redeLink,
                        'plataforma' => $redeNome,
                        'url'        => $redeLink
                    ];
                }
            }
        }

        // Upload de foto caso enviado
        $foto_url = $foto_url_manual ?: null;
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

        $max_escalas_mes    = max(0, (int)$this->request->getPost('max_escalas_mes'));
        $nickname           = trim((string)$this->request->getPost('nickname')) ?: null;
        $nivel_conhecimento = trim((string)$this->request->getPost('nivel_conhecimento')) ?: 'JUNIOR';

        $dados = [
            'id_filial'          => 1,
            'nome'               => $nome,
            'nickname'           => $nickname,
            'nivel_conhecimento' => $nivel_conhecimento,
            'email'              => $email,
            'telefone_whatsapp'  => $telefone_whatsapp,
            'data_nascimento'    => $data_nascimento,
            'status'             => $status,
            'max_escalas_mes'    => $max_escalas_mes,
            'redes_sociais'      => !empty($redesArray) ? json_encode($redesArray, JSON_UNESCAPED_UNICODE) : null,
            'observacao'         => $observacao
        ];

        if ($foto_url !== null) {
            $dados['foto_url'] = $foto_url;
        }

        $voluntarioModel = new VoluntarioModel();

        if ($id_voluntario > 0) {
            $voluntarioModel->update($id_voluntario, $dados);
            $msg = 'Voluntário atualizado com sucesso!';
        } else {
            $id_voluntario = $voluntarioModel->insert($dados);
            $hash = sha1('VOL_' . $id_voluntario . '_' . time());
            $voluntarioModel->update($id_voluntario, ['hash_voluntario' => $hash]);
            $msg = 'Voluntário cadastrado com sucesso!';
        }

        // Sincroniza Áreas / Departamentos vinculados respeitando os departamentos permitidos ao gestor
        $areaIds = $this->request->getPost('areas') ?: [];
        $voluntarioAreaModel     = new VoluntarioAreaModel();
        $departamentoGestorModel = new DepartamentoGestorModel();
        $departamentosPermitidos = $departamentoGestorModel->getDepartamentosIdsPorUsuario((int)$data['usuario']->id_usuario);

        $voluntarioAreaModel->sincronizarAreas($id_voluntario, (array)$areaIds, $departamentosPermitidos);

        // Sincroniza Disponibilidade de Cultos (N:N)
        $cultoIds = $this->request->getPost('cultos') ?: [];
        $voluntarioCultoModel = new VoluntarioCultoModel();
        $voluntarioCultoModel->sincronizarCultos($id_voluntario, (array)$cultoIds);

        session()->setFlashdata('success', $msg);
        return redirect()->to('voluntario');
    }

    /**
     * Exclui Voluntário
     */
    public function apagar($id_voluntario)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return redirect()->to('accessdeny');
        }

        $id_voluntario = (int)$id_voluntario;
        $voluntarioModel = new VoluntarioModel();
        $vol = $voluntarioModel->find($id_voluntario);

        if (!$vol) {
            session()->setFlashdata('error', 'Voluntário não encontrado.');
            return redirect()->to('voluntario');
        }

        $voluntarioModel->delete($id_voluntario);
        session()->setFlashdata('success', 'Voluntário excluído com sucesso!');
        return redirect()->to('voluntario');
    }

    /**
     * Dashboard dedicado do Voluntário
     */
    public function dashVoluntario($id_voluntario)
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $periodo = $this->request->getGet('periodo') ?: 'tudo';
        $voluntarioModel = new VoluntarioModel();
        $stats = $voluntarioModel->getEstatisticasVoluntario((int)$id_voluntario, $periodo);

        if (!$stats) {
            session()->setFlashdata('error', 'Voluntário não encontrado.');
            return redirect()->to('voluntario');
        }

        $data['stats']   = $stats;
        $data['periodo'] = $periodo;

        $data['content_view'] = view('voluntario/dash-voluntario', $data);
        return view('_layout', $data);
    }

    /**
     * AJAX: Retorna estatísticas por período
     */
    public function getEstatisticasPeriodo()
    {
        $id_voluntario = (int)$this->request->getGet('id_voluntario');
        $periodo       = (string)$this->request->getGet('periodo') ?: 'tudo';

        $voluntarioModel = new VoluntarioModel();
        $stats = $voluntarioModel->getEstatisticasVoluntario($id_voluntario, $periodo);

        if (!$stats) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Voluntário não encontrado.']);
        }

        return $this->response->setJSON(['status' => 'success', 'stats' => $stats]);
    }

    /**
     * Relatório Geral de Desempenho e Assiduidade dos Voluntários
     */
    public function desempenho()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $departamentoGestorModel = new DepartamentoGestorModel();
        $departamentosPermitidos = $departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        $filtros = [
            'periodo'              => $this->request->getGet('periodo') ?: 'mes_atual',
            'data_inicio'          => $this->request->getGet('data_inicio'),
            'data_fim'             => $this->request->getGet('data_fim'),
            'id_departamento'      => $this->request->getGet('id_departamento'),
            'id_area'              => $this->request->getGet('id_area'),
            'status'               => $this->request->getGet('status') !== null ? $this->request->getGet('status') : '1',
            'apenas_cancelamentos' => (bool)$this->request->getGet('apenas_cancelamentos'),
            'busca'                => $this->request->getGet('busca'),
            'ordem'                => $this->request->getGet('ordem') ?: 'cancelamentos_desc'
        ];

        if (!empty($filtros['id_departamento'])) {
            if (!in_array((int)$filtros['id_departamento'], $departamentosPermitidosIds)) {
                $filtros['id_departamento'] = !empty($departamentosPermitidosIds) ? $departamentosPermitidosIds[0] : -1;
            }
        } else {
            $filtros['departamentos_permitidos'] = $departamentosPermitidosIds;
        }

        $voluntarioModel = new VoluntarioModel();
        $relatorio = $voluntarioModel->getRelatorioDesempenho($filtros);

        $areaModel = new DepartamentoAreaModel();
        $areas = $areaModel->getTodasAreasAgrupadas();

        $data['relatorio']                  = $relatorio;
        $data['departamentos']              = $departamentosPermitidos;
        $data['departamentosPermitidosIds'] = $departamentosPermitidosIds;
        $data['areas']                      = $areas;
        $data['filtros']                    = $filtros;

        $data['content_view']  = view('voluntario/relatorio-desempenho', $data);
        return view('_layout', $data);
    }

    /**
     * AJAX: Retorna lista de justificativas de cancelamento de um voluntário
     */
    public function getJustificativas($id_voluntario)
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $id_voluntario = (int)$id_voluntario;
        $voluntarioModel = new VoluntarioModel();
        $vol = $voluntarioModel->find($id_voluntario);

        if (!$vol) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Voluntário não encontrado.']);
        }

        $cancelamentos = $voluntarioModel->getJustificativasCancelamento($id_voluntario);

        return $this->response->setJSON([
            'status'        => 'success',
            'voluntario'    => [
                'id'       => $vol->id_voluntario,
                'nome'     => $vol->nome,
                'nickname' => $vol->nickname,
                'telefone' => $vol->telefone_whatsapp,
                'foto_url' => $vol->foto_url
            ],
            'cancelamentos' => $cancelamentos
        ]);
    }

    /**
     * AJAX: Reseta a senha do voluntário para o seu número de WhatsApp padrão (apenas números)
     */
    public function resetSenha()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $id_voluntario = (int)$this->request->getPost('id_voluntario');
        if ($id_voluntario <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID do voluntário inválido.']);
        }

        $voluntarioModel = new VoluntarioModel();
        $voluntario = $voluntarioModel->find($id_voluntario);

        if (!$voluntario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Voluntário não encontrado.']);
        }

        $ok = $voluntarioModel->resetarSenha($id_voluntario);

        if ($ok) {
            $foneLimpo = preg_replace('/\D/', '', (string)$voluntario->telefone_whatsapp);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Senha de '{$voluntario->nome}' resetada com sucesso para o padrão ({$foneLimpo})."
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Falha ao redefinir a senha do voluntário.']);
    }

    /**
     * AJAX: Verifica se o voluntário já existe no sistema pelo número de telefone
     * GET /voluntario/verificarTelefone ou GET /api/voluntarios/verificar-telefone
     */
    public function verificarTelefone()
    {
        $telefone = $this->request->getGet('numero') 
            ?: $this->request->getGet('telefone') 
            ?: $this->request->getGet('telefone_whatsapp');

        if (empty($telefone)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'     => 'error',
                'encontrado' => false,
                'message'    => 'Número de telefone não informado.'
            ]);
        }

        $voluntarioModel = new VoluntarioModel();
        $voluntario = $voluntarioModel->buscarPorTelefone($telefone);

        if (!$voluntario) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'     => 'not_found',
                'encontrado' => false,
                'message'    => 'Nenhum voluntário encontrado com este telefone.'
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status'     => 'success',
            'encontrado' => true,
            'voluntario' => [
                'id_voluntario'      => (int)$voluntario->id_voluntario,
                'nome'               => $voluntario->nome,
                'nickname'           => $voluntario->nickname,
                'email'              => $voluntario->email,
                'telefone_whatsapp'  => $voluntario->telefone_whatsapp,
                'foto_url'           => $voluntario->foto_url,
                'nivel_conhecimento' => $voluntario->nivel_conhecimento,
                'areas'              => $voluntario->areas ?? []
            ]
        ]);
    }

    /**
     * AJAX: Vinculação rápida de voluntário existente a um departamento/sub-área
     * POST /voluntario/vincularRapido ou POST /api/voluntarios/vincular-rapido
     */
    public function vincularRapido()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update) && empty($data['sys_action']->create)) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Acesso negado.'
            ]);
        }

        $id_voluntario   = (int)$this->request->getPost('id_voluntario');
        $id_departamento = (int)$this->request->getPost('id_departamento');
        $id_subarea      = (int)($this->request->getPost('id_subarea') ?: $this->request->getPost('id_area'));

        if ($id_voluntario <= 0 || $id_departamento <= 0 || $id_subarea <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Campos obrigatórios inválidos (id_voluntario, id_departamento e id_subarea).'
            ]);
        }

        // Valida se o usuário logado tem permissão para este departamento
        $departamentoGestorModel = new DepartamentoGestorModel();
        if (!$departamentoGestorModel->usuarioTemAcessoAoDepartamento($id_departamento, $data['usuario'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Você não possui permissão para vincular voluntários a este departamento.'
            ]);
        }

        $voluntarioModel = new VoluntarioModel();
        $voluntario = $voluntarioModel->find($id_voluntario);
        if (!$voluntario) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Voluntário não encontrado.'
            ]);
        }

        $voluntarioAreaModel = new VoluntarioAreaModel();
        $ok = $voluntarioAreaModel->vincularVoluntarioSubarea($id_voluntario, $id_departamento, $id_subarea);

        if ($ok) {
            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => "Voluntário '{$voluntario->nome}' vinculado com sucesso!",
                'redirect' => base_url('voluntario')
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status'  => 'error',
            'message' => 'Erro ao vincular voluntário ao departamento/sub-área.'
        ]);
    }

    /**
     * AJAX: Retorna as sub-áreas ativas de um departamento
     */
    /**
     * AJAX: Geração de Convite (Direto ou em Lote)
     * POST /voluntario/gerarConvite
     */
    public function gerarConvite()
    {
        $data = $this->session();
        if (empty($data['sys_action']->send_invite)) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Você não possui permissão para gerar links de convite.'
            ]);
        }

        $id_departamento   = (int)$this->request->getPost('id_departamento');
        $tipo              = strtoupper(trim((string)$this->request->getPost('tipo'))) === 'LOTE' ? 'LOTE' : 'DIRETO';
        $telefone          = trim((string)$this->request->getPost('telefone'));
        $capacidade_maxima = max(1, (int)$this->request->getPost('capacidade_maxima'));
        $dias_validade     = (int)($this->request->getPost('dias_validade') ?: 30);
        $enviar_whatsapp   = (bool)$this->request->getPost('enviar_whatsapp');

        if ($id_departamento <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Selecione um departamento válido para o convite.'
            ]);
        }

        // Valida acesso ao departamento
        $departamentoGestorModel = new DepartamentoGestorModel();
        if (!$departamentoGestorModel->usuarioTemAcessoAoDepartamento($id_departamento, $data['usuario'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Você não possui permissão para gerenciar este departamento.'
            ]);
        }

        if ($tipo === 'DIRETO') {
            if (empty($telefone)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Informe o número de WhatsApp do convidado para o Convite Direto.'
                ]);
            }

            // Trava de Segurança: Verifica se o celular informado já existe na base de voluntários
            $voluntarioModel = new VoluntarioModel();
            if ($voluntarioModel->existeTelefone($telefone)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Este número de WhatsApp já pertence a um voluntário cadastrado no sistema.'
                ]);
            }
        }

        $conviteModel = new DepartamentoConviteModel();
        $idUsuarioLogado = (int)$data['usuario']->id_usuario;

        $convite = $conviteModel->criarConvite(
            $id_departamento,
            $idUsuarioLogado,
            $tipo,
            $tipo === 'DIRETO' ? $telefone : null,
            $tipo === 'LOTE' ? $capacidade_maxima : 1,
            $dias_validade
        );

        if (!$convite) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Erro ao gerar o link de convite.'
            ]);
        }

        $linkCompleto = base_url('convite/' . $convite->token);

        // Dispara mensagem via WhatsApp se solicitado
        $whatsappEnviado = false;
        if ($enviar_whatsapp && !empty($telefone)) {
            $depModel = new DepartamentoModel();
            $depInfo = $depModel->find($id_departamento);
            $nomeDep = $depInfo ? $depInfo->nome : 'ADVEC';

            $mensagem = "ADVEC: Olá! Você foi convidado para fazer parte da equipe de voluntários do departamento *{$nomeDep}*! 🚀\n\nClique no link abaixo para preencher seu pré-cadastro:\n{$linkCompleto}";

            $envio = WebhookService::dispararMensagemWhatsApp($telefone, $mensagem);
            $whatsappEnviado = $envio['success'];
        }

        return $this->response->setJSON([
            'status'           => 'success',
            'message'          => 'Link de convite gerado com sucesso!',
            'convite'          => $convite,
            'link'             => $linkCompleto,
            'whatsapp_enviado' => $whatsappEnviado
        ]);
    }

    /**
     * AJAX: Lista de convites gerados para os departamentos gerenciados
     * GET /voluntario/listarConvites
     */
    public function listarConvites()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $departamentoGestorModel    = new DepartamentoGestorModel();
        $departamentosPermitidos    = $departamentoGestorModel->getDepartamentosPermitidosPorUsuario($data['usuario']);
        $departamentosPermitidosIds = array_column($departamentosPermitidos, 'id_departamento');

        $conviteModel = new DepartamentoConviteModel();
        $convites = $conviteModel->listarConvites($departamentosPermitidosIds);

        return $this->response->setJSON([
            'status'   => 'success',
            'convites' => $convites
        ]);
    }

    /**
     * AJAX: Encerra o uso de um link de convite
     * POST /voluntario/encerrarConvite
     */
    public function encerrarConvite()
    {
        $data = $this->session();
        if (empty($data['sys_action']->send_invite)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $id_convite = (int)$this->request->getPost('id_convite');
        if ($id_convite <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'ID de convite inválido.']);
        }

        $conviteModel = new DepartamentoConviteModel();
        $convite = $conviteModel->find($id_convite);

        if (!$convite) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Convite não encontrado.']);
        }

        $departamentoGestorModel = new DepartamentoGestorModel();
        if (!$departamentoGestorModel->usuarioTemAcessoAoDepartamento($convite->id_departamento, $data['usuario'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acesso negado para este departamento.']);
        }

        $ok = $conviteModel->encerrarConvite($id_convite, (int)$data['usuario']->id_usuario);

        if ($ok) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Link de convite encerrado com sucesso!'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status'  => 'error',
            'message' => 'Erro ao encerrar convite.'
        ]);
    }

    /**
     * AJAX: Aprovação de voluntário que fez self-onboarding
     * POST /voluntario/aprovarCadastro
     */
    public function aprovarCadastro()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update) && empty($data['sys_action']->send_invite)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $id_voluntario      = (int)$this->request->getPost('id_voluntario');
        $id_departamento    = (int)$this->request->getPost('id_departamento');
        $notificar_whatsapp = (int)$this->request->getPost('notificar_whatsapp');

        $id_areas_post      = $this->request->getPost('id_area');
        if (!is_array($id_areas_post)) {
            $id_areas_post = !empty($id_areas_post) ? [$id_areas_post] : [];
        }
        $id_areas           = array_values(array_unique(array_filter(array_map('intval', $id_areas_post))));
        $nivel_conhecimento = trim((string)$this->request->getPost('nivel_conhecimento')) ?: 'JUNIOR';
        $max_escalas_mes    = max(0, (int)$this->request->getPost('max_escalas_mes'));
        $observacao         = trim((string)$this->request->getPost('observacao'));

        if ($id_voluntario <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'ID do voluntário inválido.']);
        }

        $voluntarioModel = new VoluntarioModel();
        $voluntario = $voluntarioModel->find($id_voluntario);

        if (!$voluntario) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Voluntário não encontrado.']);
        }

        $dadosAprovacao = [
            'nivel_conhecimento' => $nivel_conhecimento,
            'max_escalas_mes'    => $max_escalas_mes,
            'observacao'         => $observacao,
            'id_departamento'    => $id_departamento,
            'id_areas'           => $id_areas
        ];

        $ok = $voluntarioModel->aprovarVoluntario($id_voluntario, $dadosAprovacao);

        if ($ok) {
            $msgRetorno = "Cadastro de '{$voluntario->nome}' aprovado com sucesso!";

            // Dispara notificação via API do WhatsApp somente se solicitado
            if ($notificar_whatsapp === 1) {
                $portalUrl = base_url('portal/login');
                $nomePrimeiro = explode(' ', trim($voluntario->nome))[0];

                $depModel = new DepartamentoModel();
                $depInfo = $id_departamento > 0 ? $depModel->find($id_departamento) : null;
                $nomeDep = $depInfo ? $depInfo->nome : 'ADVEC';

                $msgWhatsApp = "ADVEC: Olá {$nomePrimeiro}! 🎉 Seu cadastro na equipe de voluntários do departamento *{$nomeDep}* foi APROVADO com sucesso!\n\nAcesse agora o seu Portal de Escalas através do link:\n{$portalUrl}\n\n*Instruções de Primeiro Acesso:*\n1. Seu login é o seu número de WhatsApp.\n2. Sua senha inicial é o seu número de WhatsApp (apenas números).\n3. Ao entrar, você validará seu acesso e definirá sua nova senha pessoal.";

                WebhookService::dispararMensagemWhatsApp($voluntario->telefone_whatsapp, $msgWhatsApp);
                $msgRetorno .= " Notificação de liberação enviada via WhatsApp.";
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $msgRetorno
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status'  => 'error',
            'message' => 'Erro ao aprovar cadastro.'
        ]);
    }

    /**
     * AJAX: Rejeição de cadastro pendente
     * POST /voluntario/rejeitarCadastro
     */
    public function rejeitarCadastro()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update) && empty($data['sys_action']->send_invite)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $id_voluntario = (int)$this->request->getPost('id_voluntario');
        $motivo        = trim((string)$this->request->getPost('motivo'));

        if ($id_voluntario <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'ID do voluntário inválido.']);
        }

        $voluntarioModel = new VoluntarioModel();
        $ok = $voluntarioModel->rejeitarVoluntario($id_voluntario, $motivo);

        if ($ok) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Cadastro rejeitado com sucesso.'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status'  => 'error',
            'message' => 'Erro ao rejeitar cadastro.'
        ]);
    }

    /**
     * AJAX: Retorna as sub-áreas ativas de um departamento
     */
    public function getSubareasPorDepartamento()
    {
        $id_departamento = (int)$this->request->getGet('id_departamento');
        if ($id_departamento <= 0) {
            return $this->response->setJSON(['status' => 'error', 'subareas' => []]);
        }

        $areaModel = new DepartamentoAreaModel();
        $subareas = $areaModel->getAreasPorDepartamento($id_departamento, true);

        return $this->response->setJSON([
            'status'   => 'success',
            'subareas' => $subareas
        ]);
    }
}



