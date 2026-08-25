<?php

namespace App\Controllers;
use App\Models\UsuarioModel;

class Sessao extends BaseController
{
    public function login()
    {
        $data = [];
        helper(['form']);

        $data['usuario'] = '';
        $data['senha'] = '';
        
        if (strtolower($this->request->getMethod()) == 'post') {

            $data['usuario'] = $this->request->getPost('txtUsuario');
            $data['senha'] = $this->request->getPost('txtSenhaAtual');

            $rules = [
                'txtUsuario' => 'required|min_length[6]|max_length[100]|validateStatus[txtUsuario,txtSenhaAtual]',
                'txtSenhaAtual' => 'required|min_length[6]|max_length[15]|validateUsuario[txtUsuario,txtSenhaAtual]',
            ];

            $errors = [
                'txtSenhaAtual' => [
                    'validateUsuario' => 'Usuário ou senha inválido',                   
                    'required' => 'Informe a senha do usuário',
                    'min_length' => 'É preciso informar no mínimo 6 carateres para a senha'
                ],
                'txtUsuario' => [
                    'required' => 'Informe um usuário válido',
                    'validateStatus' => 'Usuário inativo'
                ]
            ];

            if (!$this->validate($rules, $errors)) {
                $data['validation'] = $this->validator;
            } else {
                $model = new UsuarioModel();

                $user = $model->consultaUsuario($this->request->getPost('txtUsuario'));               
                $user->perfil_acesso = $model->retornaPerfilAcesso($user->id_usuario);
                
                $newdata = [
                    'id_usuario'  => $user->id_usuario,              
                    'nome'        => $user->nome,
                    'usuario'     => $user->usuario,
                    'logged_in'   => true,
                    'obj_user'    => $user
                ];
                
                // Grava ultimo login executado
                $user->data_ultimo_login = date('Y-m-d : H:i:s');
                $model->update($user->id_usuario, $user);
        
                //$_SESSION['dsh_usuario'] = $newdata;
                //$session = \Config\Services::session($config);
                $session = session();
                $session->set('dsh_usuario',$newdata);

                return redirect()->to(base_url('dashboard/'));
            }
        }
       
        return view('_acesso/auth/login', $data);
    }

    public function logout()
    {

        //unset($_SESSION['dsh_usuario']);
        $session = session();
        $session->destroy();
        
        return redirect()->to('/dshlogin');
    }

}