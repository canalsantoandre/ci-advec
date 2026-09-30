<?php

namespace App\Controllers;

use App\Models\WebhookModel;
use App\Models\SessionModel;
use App\Services\WebhookService;

class Webhook extends BaseController
{
    private function session()
    {
        $data = [];
        $session = new SessionModel();
        $data = $session->retornaSessao($data, 'webhook/');
        return $data;
    }

    /**
     * Listagem de Webhooks
     */
    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $busca = $this->request->getGet('busca');
        $webhookModel = new WebhookModel();

        $data['webhooks'] = $webhookModel->listaWebhooks($busca);
        $data['busca']    = $busca;

        $data['content_view'] = view('webhook/webhook-list', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Novo Webhook
     */
    public function novo()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $data['webhook'] = null;

        $data['content_view'] = view('webhook/webhook-form', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Edição de Webhook
     */
    public function editar($id_webhook)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $id_webhook = (int)$id_webhook;
        $webhookModel = new WebhookModel();
        $webhook = $webhookModel->find($id_webhook);

        if (!$webhook) {
            session()->setFlashdata('error', 'Webhook não encontrado.');
            return redirect()->to('webhook');
        }

        $data['webhook'] = $webhook;

        $data['content_view'] = view('webhook/webhook-form', $data);
        return view('_layout', $data);
    }

    /**
     * Salva Inserção ou Atualização do Webhook
     */
    public function salvar()
    {
        $data = $this->session();

        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to('webhook');
        }

        $id_webhook = (int)$this->request->getPost('id_webhook');

        if ($id_webhook > 0) {
            if (empty($data['sys_action']->update)) {
                return redirect()->to('accessdeny');
            }
        } else {
            if (empty($data['sys_action']->create)) {
                return redirect()->to('accessdeny');
            }
        }

        $nome             = trim((string)$this->request->getPost('nome'));
        $url              = trim((string)$this->request->getPost('url'));
        $instancia_padrao = trim((string)$this->request->getPost('instancia_padrao'));
        $status           = (int)$this->request->getPost('status');

        if (empty($nome) || empty($url) || empty($instancia_padrao)) {
            session()->setFlashdata('error', 'Preencha todos os campos obrigatórios (*).');
            return redirect()->back()->withInput();
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            session()->setFlashdata('error', 'A URL fornecida não é válida.');
            return redirect()->back()->withInput();
        }

        $dados = [
            'id_filial'        => 1,
            'nome'             => $nome,
            'url'              => $url,
            'instancia_padrao' => $instancia_padrao,
            'status'           => $status
        ];

        $webhookModel = new WebhookModel();

        if ($id_webhook > 0) {
            $webhookModel->update($id_webhook, $dados);
            $msg = 'Configuração de Webhook atualizada com sucesso!';
        } else {
            $webhookModel->insert($dados);
            $msg = 'Novo Webhook cadastrado com sucesso!';
        }

        session()->setFlashdata('success', $msg);
        return redirect()->to('webhook');
    }

    /**
     * Exclui Webhook
     */
    public function apagar($id_webhook)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return redirect()->to('accessdeny');
        }

        $id_webhook = (int)$id_webhook;
        $webhookModel = new WebhookModel();
        $webhook = $webhookModel->find($id_webhook);

        if (!$webhook) {
            session()->setFlashdata('error', 'Webhook não encontrado.');
            return redirect()->to('webhook');
        }

        $webhookModel->delete($id_webhook);
        session()->setFlashdata('success', 'Webhook excluído com sucesso!');
        return redirect()->to('webhook');
    }

    /**
     * AJAX: Envia mensagem de teste para validar o Webhook
     */
    public function testar()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $telefone = trim((string)$this->request->getPost('telefone'));
        $id_webhook = (int)$this->request->getPost('id_webhook');

        if (empty($telefone)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Informe o número de WhatsApp para teste.']);
        }

        $webhookModel = new WebhookModel();
        $webhook = ($id_webhook > 0) ? $webhookModel->find($id_webhook) : $webhookModel->getWebhookAtivo();

        if (!$webhook) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nenhum webhook ativo cadastrado no sistema.']);
        }

        $msgTeste = "ADVEC: Teste de integração de Webhook via instância *{$webhook->instancia_padrao}* em " . date('d/m/Y H:i:s') . ". Se você recebeu esta mensagem, a integração está 100% funcional!";

        $resultado = WebhookService::dispararMensagemWhatsApp($telefone, $msgTeste, $webhook->instancia_padrao);

        return $this->response->setJSON([
            'status'    => $resultado['success'] ? 'success' : 'error',
            'message'   => $resultado['message'],
            'http_code' => $resultado['http_code'],
            'payload'   => $resultado['payload'] ?? null,
            'response'  => $resultado['response'] ?? null
        ]);
    }
}
