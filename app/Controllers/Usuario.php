<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PerfilModel;
use App\Models\SessionModel;

class Usuario extends BaseController
{
    private function session()
    {
        $data = [];
        $session = new SessionModel();
        $data = $session->retornaSessao($data, 'usuario/');

        if ($this->isAdmin()) {
            if (!isset($data['sys_action'])) {
                $data['sys_action'] = new \stdClass();
            }
            $data['sys_action']->create = true;
            $data['sys_action']->read   = true;
            $data['sys_action']->update = true;
            $data['sys_action']->delete = true;
            $data['sys_action']->reset_password = true;
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

        $usuarioModel = new UsuarioModel();
        $data['usuarios'] = $usuarioModel->listaUsuarios($this->isAdmin());

        $data['content_view'] = view('_acesso/usuario/usuario-list', $data);
        return view('_layout', $data);
    }

    public function novo()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $perfilModel = new PerfilModel();
        $data['comboPerfil'] = $perfilModel->retornaPerfil(null);

        $data['content_view'] = view('_acesso/usuario/usuario-add', $data);
        return view('_layout', $data);
    }

    public function inserirUsuario()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('usuario/novo');
        }

        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $txtUsuarioNome = trim($this->request->getPost('txtUsuarioNome'));
        $txtUsuario = trim($this->request->getPost('txtUsuario'));
        $id_perfil = $this->request->getPost('id_perfil');

        if (empty($txtUsuarioNome) || empty($txtUsuario) || empty($id_perfil)) {
            return redirect()->back()->withInput()->with('error', 'Preencha todos os campos obrigatórios.');
        }

        $usuarioModel = new UsuarioModel();

        // Verificar duplicidade de usuário
        $exists = $usuarioModel->where('usuario', $txtUsuario)->countAllResults();
        if ($exists > 0) {
            return redirect()->back()->withInput()->with('error', 'Nome de usuário (login) já cadastrado.');
        }

        $senha = password_hash(strtolower($txtUsuario), PASSWORD_BCRYPT);

        $usuarioData = [
            'nome'               => $txtUsuarioNome,
            'usuario'            => $txtUsuario,
            'senha'              => $senha,
            'senha_usuario'      => $senha,
            'id_perfil'          => $id_perfil,
            'alterar_senha'      => 1,
            'status_usuario'     => 1,
            'data_ultima_senha'  => date('Y-m-d H:i:s'),
            'hash_user'          => sha1($txtUsuario . time())
        ];

        $id_usuario = $usuarioModel->insert($usuarioData);
        $hash_final = sha1('US' . $id_usuario);

        $usuarioModel->update($id_usuario, ['hash_user' => $hash_final]);

        return redirect()->to('usuario')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function editarUsuario($hash_user)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $usuarioModel = new UsuarioModel();
        $tb_usuario = $usuarioModel->findByColumn('hash_user', $hash_user);

        if (!$tb_usuario) {
            return redirect()->to('usuario')->with('error', 'Usuário não encontrado.');
        }

        $perfilModel = new PerfilModel();
        $data['tb_usuario'] = $tb_usuario;
        $data['comboPerfil'] = $perfilModel->retornaPerfil($tb_usuario->id_perfil);

        $data['content_view'] = view('_acesso/usuario/usuario-edit', $data);
        return view('_layout', $data);
    }

    public function atualizarUsuario()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $id_usuario = $this->request->getPost('id_usuario');
        if (!$id_usuario) {
            return redirect()->to('usuario');
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($id_usuario);

        if (!$usuario) {
            return redirect()->to('usuario');
        }

        $usuario->nome           = $this->request->getPost('txtUsuarioNome');
        $usuario->usuario        = $this->request->getPost('txtUsuario');
        $usuario->id_perfil      = $this->request->getPost('id_perfil');
        $usuario->status_usuario = $this->request->getPost('cboUsuarioStatus');

        $usuarioModel->update($id_usuario, $usuario);

        return redirect()->to('usuario')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function apagarUsuario($hash_user)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return redirect()->to('accessdeny');
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->findByColumn('hash_user', $hash_user);

        if ($usuario) {
            if ($usuario->id_usuario == 1) {
                return redirect()->to('usuario')->with('error', 'O usuário Administrador Principal (id 1) não pode ser excluído.');
            }

            $usuarioModel->delete($usuario->id_usuario);
            return redirect()->to('usuario')->with('success', 'Usuário excluído com sucesso.');
        }

        return redirect()->to('usuario')->with('error', 'Usuário não encontrado.');
    }

    public function editarSenha($hash_user = null)
    {
        $data = $this->session();
        $usuarioModel = new UsuarioModel();

        if ($hash_user) {
            $tb_usuario = $usuarioModel->findByColumn('hash_user', $hash_user);
        } else {
            $session = session();
            $userSession = $session->get('dsh_usuario')['obj_user'];
            $tb_usuario = $usuarioModel->find($userSession->id_usuario);
        }

        if (!$tb_usuario) {
            return redirect()->to('dashboard');
        }

        $data['tb_usuario'] = $tb_usuario;
        $data['content_view'] = view('_acesso/usuario/usuario-password', $data);
        return view('_layout', $data);
    }

    public function atualizarSenha()
    {
        $id_usuario             = (int)$this->request->getPost('id_usuario');
        $txtSenhaAtual          = (string)$this->request->getPost('txtSenhaAtual');
        $txtUsuarioSenhaNova    = (string)$this->request->getPost('txtUsuarioSenhaNova');
        $txtUsuarioSenhaConfirm = (string)$this->request->getPost('txtUsuarioSenhaConfirm');

        if (!$id_usuario || empty($txtSenhaAtual) || empty($txtUsuarioSenhaNova)) {
            return redirect()->back()->with('error', 'Por favor, preencha todos os campos obrigatórios.');
        }

        if (!empty($txtUsuarioSenhaConfirm) && $txtUsuarioSenhaNova !== $txtUsuarioSenhaConfirm) {
            return redirect()->back()->with('error', 'A nova senha e a confirmação de senha não coincidem.');
        }

        if (strlen($txtUsuarioSenhaNova) < 6) {
            return redirect()->back()->with('error', 'A nova senha deve ter no mínimo 6 caracteres.');
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($id_usuario);

        if (!$usuario) {
            return redirect()->to('dashboard')->with('error', 'Usuário não encontrado.');
        }

        $senhaAtualValida = false;
        if (!empty($usuario->senha) && password_verify($txtSenhaAtual, $usuario->senha)) {
            $senhaAtualValida = true;
        } elseif (!empty($usuario->senha_usuario) && password_verify($txtSenhaAtual, $usuario->senha_usuario)) {
            $senhaAtualValida = true;
        } elseif ((!empty($usuario->senha) && md5($txtSenhaAtual) === $usuario->senha) || (!empty($usuario->senha_usuario) && md5($txtSenhaAtual) === $usuario->senha_usuario)) {
            $senhaAtualValida = true;
        }

        if (!$senhaAtualValida) {
            return redirect()->back()->with('error', 'A senha atual informada está incorreta.');
        }

        $novaSenhaHash = password_hash($txtUsuarioSenhaNova, PASSWORD_BCRYPT);
        $usuarioModel->update($id_usuario, [
            'senha'             => $novaSenhaHash,
            'senha_usuario'     => $novaSenhaHash,
            'alterar_senha'     => 0,
            'data_ultima_senha' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('dashboard')->with('success', 'Sua senha foi atualizada com sucesso!');
    }

    public function getModalResetSenha($hash_user = null)
    {
        if (!$hash_user) {
            $hash_user = $this->request->getVar('hash_user');
        }

        $usuarioModel = new UsuarioModel();
        $data['usr']  = $usuarioModel->findByColumn('hash_user', $hash_user);

        $html = view('_acesso/usuario/_partial/modal-confirm-reset-senha', $data);

        return $this->response->setJSON([
            'erro'     => '0',
            'mensagem' => '',
            'modal'    => $html
        ]);
    }

    public function resetSenha()
    {
        $data = $this->session();
        if (empty($data['sys_action']->reset_password) && !$this->isAdmin()) {
            return $this->response->setJSON(['erro' => 1, 'mensagem' => 'Acesso negado.']);
        }

        $hash_user = $this->request->getPost('hash_user');
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->findByColumn('hash_user', $hash_user);

        if ($usuario) {
            $novaSenhaHash = password_hash(strtolower($usuario->usuario), PASSWORD_BCRYPT);
            $usuarioModel->update($usuario->id_usuario, [
                'senha'             => $novaSenhaHash,
                'senha_usuario'     => $novaSenhaHash,
                'alterar_senha'     => 1,
                'data_ultima_senha' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['erro' => 0, 'mensagem' => 'Senha do usuário reiniciada!']);
        }

        return $this->response->setJSON(['erro' => 1, 'mensagem' => 'Usuário não encontrado.']);
    }

    /**
     * Upload automático de foto de perfil do usuário logado via AJAX
     * POST /usuario/uploadFotoPerfil
     */
    public function uploadFotoPerfil()
    {
        $session = session();
        $userData = $session->get('dsh_usuario');
        if (empty($userData['obj_user'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'message' => 'Sessão expirada. Faça login novamente.'
            ]);
        }

        $id_usuario = (int)$userData['obj_user']->id_usuario;
        $file = $this->request->getFile('foto_file');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Nenhum arquivo válido de imagem foi enviado.'
            ]);
        }

        // Validação de tipo MIME
        $mime = $file->getMimeType();
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Formato de imagem inválido. Aceitos: JPG, PNG, WEBP.'
            ]);
        }

        // Cria diretório se não existir
        $uploadDir = FCPATH . 'uploads/usuarios';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadDir, $newName);

        $fotoUrl = base_url('uploads/usuarios/' . $newName);

        // Atualiza banco de dados
        $usuarioModel = new UsuarioModel();
        $usuarioModel->update($id_usuario, [
            'foto_url' => $fotoUrl
        ]);

        // Atualiza sessão ativa
        $userData['obj_user']->foto_url = $fotoUrl;
        $userData['obj_user']->foto = $fotoUrl;
        $session->set('dsh_usuario', $userData);

        return $this->response->setJSON([
            'status'   => 'success',
            'foto_url' => $fotoUrl,
            'message'  => 'Foto de perfil atualizada com sucesso!'
        ]);
    }
}
