<?php

namespace App\Controllers;

class Geral extends BaseController
{
    public function getModalDelete()
    {
        $data['geral_titulo']   = $this->request->getVar('geral_titulo');
        $data['geral_mensagem'] = $this->request->getVar('geral_mensagem');
        $data['geral_link']     = $this->request->getVar('geral_link');

        $data['modal-novo'] = view('_geral/modal-confirm-delete', $data);

        $erro     = "0";
        $mensagem = '';

        $arr = array(
            'mensagem' => $mensagem,
            'erro'     => $erro,
            'tela'     => '',
            'modal'    => $data['modal-novo']
        );

        return $this->response->setJSON($arr);
    }

}
