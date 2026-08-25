<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use App\Models\SessionModel;
use App\Models\PerfilModuloModel;
use App\Models\PerfilModuloAcaoModel;

class Perfil extends BaseController
{
    private function session()
    {
        $data = [];
        $session = new SessionModel();
        $data = $session->retornaSessao($data, 'perfil/');

        if ($this->isAdmin()) {
            if (!isset($data['sys_action'])) {
                $data['sys_action'] = new \stdClass();
            }
            $data['sys_action']->create = true;
            $data['sys_action']->read   = true;
            $data['sys_action']->update = true;
            $data['sys_action']->delete = true;
        }

        return $data;
    }

    private function isAdmin()
    {
        $session = session();
        $userData = $session->get('dsh_usuario');
        if (empty($userData['obj_user'])) {
            return false;
        }
        $user = $userData['obj_user'];
        $perfilModel = new PerfilModel();
        $perfil = $perfilModel->find($user->id_perfil);
        if ($perfil && (intval($user->id_usuario) === 1 || strtoupper(trim($perfil->nome_perfil)) === 'SYSADM')) {
            return true;
        }
        return false;
    }

    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $perfilModel = new PerfilModel();
        $data['perfil'] = $perfilModel->findAll();

        $data['content_view'] = view('_acesso/perfil/perfil-list', $data);
        return view('_layout', $data);
    }

    public function novoPerfil()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $data['content_view'] = view('_acesso/perfil/perfil-add', $data);
        return view('_layout', $data);
    }

    public function inserirPerfil()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $nome_perfil = trim($this->request->getPost('txtPerfilNome') ?? '');
        $content_view = trim($this->request->getPost('txtContentViewDefault') ?? '_main/principal');
        $status_perfil = $this->request->getPost('cboPerfilStatus') ?? 1;

        if (empty($nome_perfil)) {
            return redirect()->back()->withInput()->with('error', 'O nome do perfil é obrigatório.');
        }

        $perfilModel = new PerfilModel();
        
        $db = db_connect();
        $maxRow = $db->table('tb_sys_perfil')->selectMax('id_perfil')->get()->getFirstRow();
        $id_perfil = (!empty($maxRow->id_perfil)) ? ($maxRow->id_perfil + 1) : 1;

        $perfilModel->insert([
            'id_perfil'            => $id_perfil,
            'nome_perfil'           => $nome_perfil,
            'content_view_default' => $content_view,
            'status_perfil'        => (int)$status_perfil
        ]);

        return redirect()->to('perfil/editar/' . $id_perfil)->with('success', 'Perfil criado com sucesso! Configure as permissões abaixo.');
    }

    public function editarPerfil($id_perfil)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $perfilModel = new PerfilModel();
        $data['perfil'] = $perfilModel->find($id_perfil);

        if (!$data['perfil']) {
            return redirect()->to('perfil')->with('error', 'Perfil não encontrado.');
        }

        $perfilModulo = $perfilModel->perfilModulo($id_perfil);

        $result = array();
        foreach ($perfilModulo as $row) {
            $c = $perfilModel->perfilModuloAcao($row->id_modulo, $id_perfil);
            $row->perfil_acoes = $c;

            $subitem = $perfilModel->perfilModulo($id_perfil, $row->id_modulo);
            $row->subitem = $subitem;

            $result[] = $row;
        }
        $data['perfil_modulo'] = $result;

        $data['content_view'] = view('_acesso/perfil/perfil-edit', $data);
        return view('_layout', $data);
    }

    public function atualizarPerfil()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $id_perfil = $this->request->getPost('id_perfil');
        if (!$id_perfil) {
            return redirect()->to('perfil');
        }

        $perfilModel = new PerfilModel();
        $perfil = $perfilModel->find($id_perfil);

        $perfil->nome_perfil = $this->request->getPost('txtPerfilNome');
        $perfil->status_perfil = $this->request->getPost('cboPerfilStatus');

        $perfilModel->update($id_perfil, $perfil);

        return redirect()->to('perfil')->with('success', 'Perfil atualizado com sucesso.');
    }

    public function apagarPerfil($id_perfil)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return redirect()->to('accessdeny');
        }

        if ($id_perfil == 1) {
            return redirect()->to('perfil')->with('error', 'O perfil SysAdm não pode ser excluído.');
        }

        $db = db_connect();
        $userCount = $db->table('tb_sys_usuario')->where('id_perfil', $id_perfil)->countAllResults();
        if ($userCount > 0) {
            return redirect()->to('perfil')->with('error', 'Existem usuários vinculados a este perfil.');
        }

        $db->transStart();
        $db->table('tb_sys_perfil_modulo_acao')->where('id_perfil', $id_perfil)->delete();
        $db->table('tb_sys_perfil_modulo')->where('id_perfil', $id_perfil)->delete();
        $db->table('tb_sys_perfil')->where('id_perfil', $id_perfil)->delete();
        $db->transComplete();

        return redirect()->to('perfil')->with('success', 'Perfil excluído com sucesso.');
    }

    public function atualizarModulo()
    {
        $nome_modulo = $this->request->getVar('nome_modulo') ?? '';
        $id_perfil   = $this->request->getVar('id_perfil');
        $id_modulo   = $this->request->getVar('id_modulo');
        $status      = $this->request->getVar('status');

        $perfilModuloModel = new PerfilModuloModel();
        if ($status == 0) {
            $detalheModel = new PerfilModuloAcaoModel();
            $detalheModel->where('id_perfil', $id_perfil)->where('id_modulo', $id_modulo)->delete();
            $perfilModuloModel->where('id_perfil', $id_perfil)->where('id_modulo', $id_modulo)->delete();
        } else {
            $exists = $perfilModuloModel->where('id_perfil', $id_perfil)->where('id_modulo', $id_modulo)->countAllResults();
            if ($exists == 0) {
                $perfilModuloModel->insert([
                    'id_perfil' => $id_perfil,
                    'id_modulo' => $id_modulo
                ]);
            }
        }

        return $this->response->setJSON([
            'erro'     => '0',
            'mensagem' => 'Permissão do módulo atualizada com sucesso'
        ]);
    }

    public function atualizarModuloAcao()
    {
        $id_perfil   = $this->request->getVar('id_perfil');
        $id_modulo   = $this->request->getVar('id_modulo');
        $modulo_acao = $this->request->getVar('modulo_acao');
        $status      = $this->request->getVar('status');

        $model = new PerfilModuloAcaoModel();
        if ($status == 0) {
            $model->where('id_perfil', $id_perfil)->where('id_modulo', $id_modulo)->where('modulo_acao', $modulo_acao)->delete();
        } else {
            $exists = $model->where('id_perfil', $id_perfil)->where('id_modulo', $id_modulo)->where('modulo_acao', $modulo_acao)->countAllResults();
            if ($exists == 0) {
                $model->insert([
                    'id_perfil'   => $id_perfil,
                    'id_modulo'   => $id_modulo,
                    'modulo_acao' => $modulo_acao
                ]);
            }
        }

        return $this->response->setJSON([
            'erro'     => '0',
            'mensagem' => 'Ação do módulo atualizada com sucesso'
        ]);
    }
}
