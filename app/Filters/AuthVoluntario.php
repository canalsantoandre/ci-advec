<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\VoluntarioModel;

class AuthVoluntario implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->has('dsh_voluntario') || empty($session->get('dsh_voluntario')['logged_in'])) {
            return redirect()->to(base_url('portal/login'));
        }

        $idVoluntario = (int)($session->get('dsh_voluntario')['id_voluntario'] ?? 0);
        if ($idVoluntario <= 0) {
            $session->remove('dsh_voluntario');
            $session->remove('otp_auth_challenge');
            session()->setFlashdata('erro_login', 'Sessão inválida. Por favor, faça login novamente.');
            return redirect()->to(base_url('portal/login'));
        }

        $voluntarioModel = new VoluntarioModel();
        $vol = $voluntarioModel->find($idVoluntario);

        if (!$vol || $vol->status != 1 || (isset($vol->status_aprovacao) && $vol->status_aprovacao === 'PENDENTE')) {
            $session->remove('dsh_voluntario');
            $session->remove('otp_auth_challenge');
            
            if (!$vol) {
                session()->setFlashdata('erro_login', 'Seu cadastro de voluntário não foi encontrado na base de dados. Caso necessário, realize um novo pré-cadastro ou contate o líder do departamento.');
            } elseif (isset($vol->status_aprovacao) && $vol->status_aprovacao === 'PENDENTE') {
                session()->setFlashdata('erro_login', 'Seu pré-cadastro está aguardando aprovação do líder.');
            } else {
                session()->setFlashdata('erro_login', 'Seu cadastro de voluntário está inativo. Fale com o líder do seu departamento.');
            }

            return redirect()->to(base_url('portal/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Headers no-cache para evitar que o navegador armazene telas protegidas em cache
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        return $response;
    }
}

