<?php

namespace App\Controllers;

class Accessdeny extends BaseController
{
    public function index()
    {
        $data = [];
        $session = session();
        if ($session->get('dsh_usuario')) {
            $data['usuario'] = $session->get('dsh_usuario')['obj_user'];
        }

        $data['title'] = 'Acesso Negado - ADVEC';
        $data['content_view'] = view('errors/access_deny', $data);

        return view('_layout', $data);
    }
}
