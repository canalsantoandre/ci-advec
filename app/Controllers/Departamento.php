<?php

namespace App\Controllers;

use App\Models\DepartamentoModel;
use App\Models\DepartamentoAreaModel;
use App\Models\SessionModel;

class Departamento extends BaseController
{
    private function session()
    {
        $data = [];
        $session = new SessionModel();
        $data = $session->retornaSessao($data, 'departamento/');
        return $data;
    }

    /**
     * Listagem de Departamentos
     */
    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $departamentoModel = new DepartamentoModel();
        $data['departamentos'] = $departamentoModel->getDepartamentosComContadores();

        $data['content_view'] = view('departamento/departamento-list', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Novo Departamento
     */
    public function novo()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $data['departamento'] = null;
        $data['areas']        = [];

        $data['content_view'] = view('departamento/departamento-form', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Edição de Departamento
     */
    public function editar($id_departamento)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $id_departamento = (int)$id_departamento;
        $departamentoModel = new DepartamentoModel();
        $departamento = $departamentoModel->find($id_departamento);

        if (!$departamento) {
            session()->setFlashdata('error', 'Departamento não encontrado.');
            return redirect()->to('departamento');
        }

        $departamentoAreaModel = new DepartamentoAreaModel();
        $data['departamento']  = $departamento;
        $data['areas']         = $departamentoAreaModel->getAreasPorDepartamento($id_departamento);

        $data['content_view']  = view('departamento/departamento-form', $data);
        return view('_layout', $data);
    }

    /**
     * Salva ou Atualiza Departamento
     */
    public function salvar()
    {
        $data = $this->session();
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to('departamento');
        }

        $id_departamento = (int)$this->request->getPost('id_departamento');

        if ($id_departamento > 0) {
            if (empty($data['sys_action']->update)) {
                return redirect()->to('accessdeny');
            }
        } else {
            if (empty($data['sys_action']->create)) {
                return redirect()->to('accessdeny');
            }
        }

        $departamentoModel = new DepartamentoModel();

        $nome                 = trim((string)$this->request->getPost('nome'));
        $responsavel_nome     = trim((string)$this->request->getPost('responsavel_nome'));
        $responsavel_telefone = trim((string)$this->request->getPost('responsavel_telefone'));
        $descricao            = trim((string)$this->request->getPost('descricao'));
        $cor_identificacao    = (string)$this->request->getPost('cor_identificacao') ?: '#2563eb';
        $status               = (int)$this->request->getPost('status');
        $logo_url_manual      = trim((string)$this->request->getPost('logo_url_manual'));

        if (empty($nome) || empty($responsavel_nome) || empty($responsavel_telefone)) {
            session()->setFlashdata('error', 'Preencha todos os campos obrigatórios (*).');
            return redirect()->back()->withInput();
        }

        $logo_url = $logo_url_manual ?: null;

        // Trata upload de logo caso fornecido
        $fileLogo = $this->request->getFile('logo_file');
        if ($fileLogo && $fileLogo->isValid() && !$fileLogo->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/departamentos';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $newName = $fileLogo->getRandomName();
            $fileLogo->move($uploadDir, $newName);
            $logo_url = base_url('uploads/departamentos/' . $newName);
        }

        $dados = [
            'id_filial'            => 1,
            'nome'                 => $nome,
            'descricao'            => $descricao,
            'responsavel_nome'     => $responsavel_nome,
            'responsavel_telefone' => $responsavel_telefone,
            'cor_identificacao'    => $cor_identificacao,
            'status'               => $status
        ];

        if ($logo_url !== null) {
            $dados['logo_url'] = $logo_url;
        }

        if ($id_departamento > 0) {
            $departamentoModel->update($id_departamento, $dados);
            session()->setFlashdata('success', 'Departamento atualizado com sucesso!');
        } else {
            $id_departamento = $departamentoModel->insert($dados);
            session()->setFlashdata('success', 'Departamento cadastrado com sucesso! Agora você pode adicionar as sub-áreas.');
            return redirect()->to("departamento/editar/{$id_departamento}");
        }

        return redirect()->to('departamento');
    }

    /**
     * Exclui Departamento
     */
    public function apagar($id_departamento)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return redirect()->to('accessdeny');
        }

        $id_departamento = (int)$id_departamento;
        $departamentoModel = new DepartamentoModel();
        $dep = $departamentoModel->find($id_departamento);

        if (!$dep) {
            session()->setFlashdata('error', 'Departamento não encontrado.');
            return redirect()->to('departamento');
        }

        // Verifica se há escalas vinculadas
        $db = db_connect();
        $temEscalas = $db->table('tb_escala_voluntario')
            ->where('id_departamento', $id_departamento)
            ->countAllResults();

        if ($temEscalas > 0) {
            session()->setFlashdata('error', "Não é possível excluir o departamento '{$dep->nome}', pois existem agendamentos/escalas vinculadas.");
            return redirect()->to('departamento');
        }

        $departamentoModel->delete($id_departamento);
        session()->setFlashdata('success', 'Departamento excluído com sucesso!');
        return redirect()->to('departamento');
    }

    /**
     * AJAX: Salva ou atualiza sub-área do departamento
     */
    public function salvarArea()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update) && empty($data['sys_action']->create)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $id_area         = (int)$this->request->getPost('id_area');
        $id_departamento = (int)$this->request->getPost('id_departamento');
        $nome_area       = trim((string)$this->request->getPost('nome_area'));
        $descricao       = trim((string)$this->request->getPost('descricao'));
        $status          = (int)$this->request->getPost('status');

        if (empty($nome_area) || $id_departamento <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nome da área é obrigatório.']);
        }

        $areaModel = new DepartamentoAreaModel();

        $dados = [
            'id_departamento' => $id_departamento,
            'nome_area'       => $nome_area,
            'descricao'       => $descricao,
            'status'          => $status
        ];

        if ($id_area > 0) {
            $areaModel->update($id_area, $dados);
            $msg = 'Sub-área atualizada com sucesso!';
        } else {
            $id_area = $areaModel->insert($dados);
            $msg = 'Sub-área adicionada com sucesso!';
        }

        $areas = $areaModel->getAreasPorDepartamento($id_departamento);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $msg,
            'id_area' => $id_area,
            'areas'   => $areas
        ]);
    }

    /**
     * AJAX: Exclui sub-área
     */
    public function excluirArea()
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete) && empty($data['sys_action']->update)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $id_area = (int)$this->request->getPost('id_area');
        $areaModel = new DepartamentoAreaModel();
        $area = $areaModel->find($id_area);

        if (!$area) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sub-área não encontrada.']);
        }

        if ($areaModel->emUsoEmEscalas($id_area)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Atenção! Esta sub-área possui escalas cadastradas e não pode ser excluída.'
            ]);
        }

        $id_departamento = $area->id_departamento;
        $areaModel->delete($id_area);
        $areas = $areaModel->getAreasPorDepartamento($id_departamento);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Sub-área removida com sucesso!',
            'areas'   => $areas
        ]);
    }

    /**
     * AJAX: Retorna JSON de áreas de um departamento
     */
    public function getAreasByDepartamento($id_departamento)
    {
        $areaModel = new DepartamentoAreaModel();
        $areas = $areaModel->getAreasPorDepartamento((int)$id_departamento, true);
        return $this->response->setJSON($areas);
    }
}
