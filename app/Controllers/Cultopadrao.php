<?php

namespace App\Controllers;

use App\Models\CultoPadraoModel;

class Cultopadrao extends BaseController
{
    private function session()
    {
        $data = [];
        $session = new \App\Models\SessionModel();
        $data = $session->retornaSessao($data, 'cultopadrao/');
        return $data;
    }

    /**
     * Lista de Cultos Padrão (Tipos de Culto)
     */
    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $cultoPadraoModel = new CultoPadraoModel();
        $data['cultosPadrao']     = $cultoPadraoModel->orderBy('dia_semana', 'ASC')
            ->orderBy('horario_inicio', 'ASC')
            ->findAll();
        $data['diasSemana']       = CultoPadraoModel::getDiasSemana();
        $data['tiposRecorrencia'] = CultoPadraoModel::getTiposRecorrencia();

        $data['content_view'] = view('culto_padrao/culto-padrao-list', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Novo Culto Padrão
     */
    public function novo()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $data['diasSemana']       = CultoPadraoModel::getDiasSemana();
        $data['tiposRecorrencia'] = CultoPadraoModel::getTiposRecorrencia();
        $data['cultoPadrao']      = null;

        $data['content_view'] = view('culto_padrao/culto-padrao-form', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Edição de Culto Padrão
     */
    public function editar($id_culto_padrao)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $cultoPadraoModel = new CultoPadraoModel();
        $cultoPadrao = $cultoPadraoModel->find((int)$id_culto_padrao);

        if (!$cultoPadrao) {
            session()->setFlashdata('error', 'Culto padrão não encontrado.');
            return redirect()->to('cultopadrao');
        }

        $data['cultoPadrao']      = $cultoPadrao;
        $data['diasSemana']       = CultoPadraoModel::getDiasSemana();
        $data['tiposRecorrencia'] = CultoPadraoModel::getTiposRecorrencia();

        $data['content_view'] = view('culto_padrao/culto-padrao-form', $data);
        return view('_layout', $data);
    }

    /**
     * Salva (Inserção ou Atualização) de Culto Padrão
     */
    public function salvar()
    {
        $data = $this->session();

        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to('cultopadrao');
        }

        $id_culto_padrao = (int)$this->request->getPost('id_culto_padrao');

        if ($id_culto_padrao > 0) {
            if (empty($data['sys_action']->update)) {
                return redirect()->to('accessdeny');
            }
        } else {
            if (empty($data['sys_action']->create)) {
                return redirect()->to('accessdeny');
            }
        }

        $cultoPadraoModel = new CultoPadraoModel();
        $posicaoSemanaRaw = $this->request->getPost('posicao_semana');

        $dados = [
            'id_filial'        => 1,
            'nome_culto'       => trim((string)$this->request->getPost('nome_culto')),
            'dia_semana'       => (int)$this->request->getPost('dia_semana'),
            'horario_inicio'   => (string)$this->request->getPost('horario_inicio'),
            'horario_termino'  => (string)$this->request->getPost('horario_termino'),
            'descricao'        => trim((string)$this->request->getPost('descricao')),
            'cor_evento'       => (string)$this->request->getPost('cor_evento') ?: '#2563eb',
            'tipo_recorrencia' => (string)$this->request->getPost('tipo_recorrencia') ?: 'todas',
            'posicao_semana'   => ($posicaoSemanaRaw !== '' && $posicaoSemanaRaw !== null) ? (int)$posicaoSemanaRaw : null,
            'status_culto'     => (int)$this->request->getPost('status_culto')
        ];

        if (empty($dados['nome_culto'])) {
            session()->setFlashdata('error', 'O nome do culto é obrigatório.');
            return redirect()->back()->withInput();
        }

        if ($id_culto_padrao > 0) {
            $cultoPadraoModel->update($id_culto_padrao, $dados);
            session()->setFlashdata('success', 'Culto padrão atualizado com sucesso!');
        } else {
            $cultoPadraoModel->insert($dados);
            session()->setFlashdata('success', 'Culto padrão cadastrado com sucesso!');
        }

        return redirect()->to('cultopadrao');
    }

    /**
     * Exclui um Culto Padrão (com verificação de uso em agendamentos)
     */
    public function apagar($id_culto_padrao)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return redirect()->to('accessdeny');
        }

        $id_culto_padrao = (int)$id_culto_padrao;
        $cultoPadraoModel = new CultoPadraoModel();

        $cultoPadrao = $cultoPadraoModel->find($id_culto_padrao);
        if (!$cultoPadrao) {
            session()->setFlashdata('error', 'Culto padrão não encontrado.');
            return redirect()->to('cultopadrao');
        }

        // Bloqueio de exclusão caso esteja em uso em agendamentos
        if ($cultoPadraoModel->emUsoNaAgenda($id_culto_padrao)) {
            session()->setFlashdata('error', 'Atenção! Não é possível excluir o culto "' . esc($cultoPadrao->nome_culto) . '" pois ele está sendo utilizado em agendamentos da agenda.');
            return redirect()->to('cultopadrao');
        }

        $cultoPadraoModel->delete($id_culto_padrao);
        session()->setFlashdata('success', 'Culto padrão excluído com sucesso!');

        return redirect()->to('cultopadrao');
    }
}
