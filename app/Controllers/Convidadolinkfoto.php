<?php

namespace App\Controllers;

class Convidadolinkfoto extends BaseController
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

   
    public function inserir()
    {

        $data = $this->session();
      
        /**************************************** */
        /**INSERIR O ITEM */
        /**************************************** */
        $id_convidado = $this->request->getGetPost('id_convidado');
        $linkFoto = new \App\Models\ConvidadoLinkFotoModel();

        $linkFoto->set('id_convidado', $this->request->getPost('id_convidado'));
        $linkFoto->set('descricao_link_foto', $this->request->getPost('descricao_link_foto'));
        $linkFoto->set('link_foto', $this->request->getPost('link_foto'));

        $linkFoto->insert();
        $id_convidado_link_foto = $linkFoto->insertID();
        /**************************************** */

        $convidadoModel = new \App\Models\ConvidadoModel();
        $data['convidado'] = $convidadoModel->find($id_convidado);

        /**CONSULTA TODOS OS ITENS DO PEDIDO PARA ALIMENTAR A TABELA EM TELA */
        $linkfotoModel = new \App\Models\ConvidadoLinkFotoModel();
        $data['convidado_link_foto'] = $linkfotoModel->getConvidadoLinkFoto($id_convidado);
        $data['LinkFotos'] = view('convidado/_partial/link-fotos', $data);

        $mensagem = 'Registro gerado com sucesso';
        $erro     = "0";

        $arr = array(
            'mensagem' => $mensagem,
            'erro' => $erro,
            'tela' => '',
            'objeto_retorno' => $data['LinkFotos']
        );

        echo  json_encode($arr);
    }
   

    public function apagar($id_convidado_link_foto)
    {
        $data = $this->session();

        $model = new \App\Models\ConvidadoLinkFotoModel();
        $produtoPedido = $model->find($id_convidado_link_foto);
        $id_convidado = $produtoPedido->id_convidado;

        $pedidoProdutoModel = new \App\Models\ConvidadoLinkFotoModel();
        $pedidoProdutoModel->where('id_convidado_link_foto', $id_convidado_link_foto);
        $pedidoProdutoModel->delete();

        $convidadoModel = new \App\Models\ConvidadoModel();
        $data['convidado'] = $convidadoModel->find($id_convidado);

        /**CONSULTA TODOS OS ITENS DO PEDIDO PARA ALIMENTAR A TABELA EM TELA */
        $linkfotoModel = new \App\Models\ConvidadoLinkFotoModel();
        $data['convidado_link_foto'] = $linkfotoModel->getConvidadoLinkFoto($id_convidado);
        $data['LinkFotos'] = view('convidado/_partial/link-fotos', $data);

        $mensagem = 'O registro foi excluso!';
        $erro     = "0";

        $arr = array(
            'mensagem' => $mensagem,
            'erro' => $erro,
            'tela' => '',
            'objeto_retorno' => $data['LinkFotos']
        );

        echo  json_encode($arr);
    }
}
