<?php

namespace App\Controllers;

use App\Models\VoluntarioModel;
use App\Models\VoluntarioAreaModel;
use App\Models\DepartamentoModel;
use App\Models\DepartamentoAreaModel;
use App\Models\SessionModel;

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

        $filtros = [
            'id_departamento' => $this->request->getGet('id_departamento'),
            'status'          => $this->request->getGet('status'),
            'busca'           => $this->request->getGet('busca')
        ];

        $voluntarioModel = new VoluntarioModel();
        $data['voluntarios'] = $voluntarioModel->listaVoluntarios($filtros);

        $departamentoModel = new DepartamentoModel();
        $data['departamentos'] = $departamentoModel->getDepartamentosAtivos();
        $data['filtros']       = $filtros;

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

        $areaModel = new DepartamentoAreaModel();
        $data['departamentosComAreas'] = $areaModel->getTodasAreasAgrupadas();
        $data['voluntario']            = null;
        $data['areasSelecionadasIds']  = [];
        $data['redesSociais']          = [];
        $data['stats']                 = null;

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

        $voluntarioAreaModel = new VoluntarioAreaModel();
        $areaModel           = new DepartamentoAreaModel();

        $data['voluntario']            = $voluntario;
        $data['areasSelecionadasIds']  = $voluntarioAreaModel->getIdsAreasDoVoluntario($voluntario->id_voluntario);
        $data['departamentosComAreas'] = $areaModel->getTodasAreasAgrupadas();

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
                        'rede' => $redeNome,
                        'link' => $redeLink
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

        // Sincroniza Áreas / Departamentos vinculados
        $areaIds = $this->request->getPost('areas') ?: [];
        $voluntarioAreaModel = new VoluntarioAreaModel();
        $voluntarioAreaModel->sincronizarAreas($id_voluntario, (array)$areaIds);

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

        $voluntarioModel = new VoluntarioModel();
        $relatorio = $voluntarioModel->getRelatorioDesempenho($filtros);

        $departamentoModel = new DepartamentoModel();
        $departamentos = $departamentoModel->getDepartamentosAtivos();

        $areaModel = new DepartamentoAreaModel();
        $areas = $areaModel->getTodasAreasAgrupadas();

        $data['relatorio']     = $relatorio;
        $data['departamentos'] = $departamentos;
        $data['areas']         = $areas;
        $data['filtros']       = $filtros;

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
}
