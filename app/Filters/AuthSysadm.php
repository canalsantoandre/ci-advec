<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\PerfilModel;

class AuthSysadm implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->has('dsh_usuario')) {
            if ($request->isAJAX()) {
                return service('response')->setJSON([
                    'status'  => 'error',
                    'message' => 'Sessão expirada. Faça login novamente.'
                ])->setStatusCode(401);
            }
            return redirect()->to('dshlogin');
        }

        $userData = $session->get('dsh_usuario');
        $user = $userData['obj_user'] ?? null;

        if (!$user) {
            if ($request->isAJAX()) {
                return service('response')->setJSON([
                    'status'  => 'error',
                    'message' => 'Usuário não autenticado.'
                ])->setStatusCode(401);
            }
            return redirect()->to('dshlogin');
        }

        // Validação Estrita do Perfil SysAdm
        $isSysAdm = false;
        if (isset($user->id_perfil) && (int)$user->id_perfil === 1) {
            $isSysAdm = true;
        } elseif (isset($user->id_usuario) && (int)$user->id_usuario === 1) {
            $isSysAdm = true;
        } else {
            $perfilModel = new PerfilModel();
            $perfil = $perfilModel->find($user->id_perfil ?? 0);
            if ($perfil && strtoupper(trim($perfil->nome_perfil)) === 'SYSADM') {
                $isSysAdm = true;
            }
        }

        if (!$isSysAdm) {
            if ($request->isAJAX()) {
                return service('response')->setJSON([
                    'status'  => 'error',
                    'message' => 'Acesso negado. Módulo restrito exclusivamente a administradores SysAdm.'
                ])->setStatusCode(403);
            }
            return redirect()->to('accessdeny');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Pós-processamento caso necessário
    }
}
