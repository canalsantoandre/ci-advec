<?php

namespace App\Controllers;

class Convidado extends BaseController
{

    private function session (){
        /* ------------------------------------------------ */
        /* RECUPERA ACESSOS E DADOS DE ACAO DAS TELAS */
        /* ------------------------------------------------ */
        $data = [];
        $session = new \App\Models\SessionModel ();
        $data = $session->retornaSessao($data, 'convidado/');
        
        return $data;
    }
    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $convidadoModel = new \App\Models\ConvidadoModel();
        $data['convidados'] =  $convidadoModel->listaConvidados();

        $db = db_connect();
        $data['funcoesEclesiasticas'] = $db->table('tb_funcao_eclesiastica')
            ->orderBy('nm_funcao_eclesiastica', 'ASC')
            ->get()
            ->getResult();

        $data['totalCultosGeral'] = $db->table('tb_agenda_culto')
            ->where('status_culto', 1)
            ->where('data_culto <=', date('Y-m-d'))
            ->countAllResults();

        $data['content_view'] = view('convidado/convidado-list', $data);
        return view('_layout', $data);
    }

    public function novo()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $funcaoModel = new \App\Models\FuncaoModel();
        $data['comboFuncao'] = $funcaoModel->retornaFuncao(null);

        $data['content_view'] = view('convidado/convidado-add', $data);
        return view('_layout', $data);
    }

    public function inserirConvidado()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        if (!strtolower($this->request->getMethod()) === 'post') {
            return redirect()->to('convidado/novo');
        }

        $convidadoModel = new \App\Models\ConvidadoModel();
        $senha = password_hash('convidado123', PASSWORD_BCRYPT);

        $convidadoModel->set('nome_convidado', $this->request->getPost('txtConvidadoNome'));
        $convidadoModel->set('nome_convidado_visualizacao', $this->request->getPost('txtConvidadoNomeExibicao'));
        $convidadoModel->set('email', $this->request->getPost('txtConvidadoEmail'));
        $convidadoModel->set('telefone', $this->request->getPost('txtConvidadoTelefone'));
        $convidadoModel->set('id_funcao_eclesiastica', $this->request->getPost('id_funcao_eclesiastica'));
        $convidadoModel->set('nick_instagram', $this->request->getPost('txtConvidadoInstagram'));
        $convidadoModel->set('link_fotos', $this->request->getPost('txtConvidadoLinkFotos'));
        $convidadoModel->set('observacao', $this->request->getPost('txtConvidadoObservacao'));
        
        $convidadoModel->set('senha_usuario', $senha);
        $convidadoModel->set('status_convidado', 1);

        $convidadoModel->insert();
        $id_convidado= $convidadoModel->insertID();

        $convidado = $convidadoModel->find($id_convidado);
        $convidado->hash_convidado = sha1('US' . $id_convidado);

        $convidadoModel->update($id_convidado, $convidado);

        return redirect()->to('convidado');
    }

    public function editarConvidado($hash_convidado)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $convidadoModel = new \App\Models\ConvidadoModel();
        $data['convidado'] = $convidadoModel->findByColumn('hash_convidado', $hash_convidado);

        if ($data['convidado']) { 
            $funcaoModel = new \App\Models\FuncaoModel();
            $data['comboFuncao'] = $funcaoModel->retornaFuncao($data['convidado']->id_funcao_eclesiastica);

            $linkfotoModel = new \App\Models\ConvidadoLinkFotoModel();
            $data['convidado_link_foto'] = $linkfotoModel->getConvidadoLinkFoto($data['convidado']->id_convidado);
            $data['LinkFotos'] = view('convidado/_partial/link-fotos', $data);

            $data['content_view'] = view('convidado/convidado-edit', $data);
            return view('_layout', $data);
        } else {
            return redirect()->to('convidado');
        }
    }

    public function atualizarConvidado()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        if (!$this->request->getPost('id_convidado')) {
            return redirect()->to('convidado');
        }
        $id_convidado = $this->request->getPost('id_convidado');

        $convidadoModel = new \App\Models\ConvidadoModel();
        $convidado = $convidadoModel->find($id_convidado);

        $convidado->nome_convidado               = $this->request->getPost('txtConvidadoNome');
        $convidado->nome_convidado_visualizacao  = $this->request->getPost('txtConvidadoNomeExibicao');
        $convidado->email                        = $this->request->getPost('txtConvidadoEmail');
        $convidado->nick_instagram               = $this->request->getPost('txtConvidadoInstagram');
        $convidado->url_foto_instagram           = $this->request->getPost('txtConvidadoFotoUrl');
        $convidado->data_nascimento              = $this->request->getPost('txtConvidadoDataNascimento');
        $convidado->link_fotos                   = $this->request->getPost('txtConvidadoLinkFotos');
        $convidado->observacao                   = $this->request->getPost('txtConvidadoObservacao');
        $convidado->telefone                     = $this->request->getPost('txtConvidadoTelefone');
        $convidado->id_funcao_eclesiastica       = $this->request->getPost('id_funcao_eclesiastica');
        $convidado->status_convidado             = $this->request->getPost('cboConvidadoStatus');

        $convidadoModel->update($id_convidado, $convidado);
        session()->setFlashdata('success', 'Dados do convidado atualizados com sucesso!');
        return redirect()->to('convidado');
    }

    public function apagarConvidado($hash_convidado)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return redirect()->to('accessdeny');
        }

        if (!$hash_convidado) {
            return redirect()->to('convidado');
        }

        $convidadoModel = new \App\Models\ConvidadoModel();
        $convidadoModel->where('hash_convidado', $hash_convidado);
        $convidadoModel->delete();

        return redirect()->to('convidado');
    }
}
