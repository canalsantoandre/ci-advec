<?php

namespace App\Controllers;

use App\Models\ApiClientModel;
use App\Models\SessionModel;
use App\Libraries\JwtService;

class ApiClient extends BaseController
{
    private function session()
    {
        $data = [];
        $session = new SessionModel();
        $data = $session->retornaSessao($data, 'api-client/');
        return $data;
    }

    /**
     * Listagem de Aplicações e Credenciais da API
     */
    public function index()
    {
        $data = $this->session();
        if (empty($data['sys_action']->read)) {
            return redirect()->to('accessdeny');
        }

        $busca = $this->request->getGet('busca');
        $apiClientModel = new ApiClientModel();

        $data['clientes'] = $apiClientModel->listarClientes($busca);
        $data['busca']    = $busca;
        $data['baseUrlApi'] = base_url('api/v1');

        $data['content_view'] = view('api_client/api_client-list', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Nova Aplicação de API
     */
    public function novo()
    {
        $data = $this->session();
        if (empty($data['sys_action']->create)) {
            return redirect()->to('accessdeny');
        }

        $data['cliente'] = null;
        $data['sugestaoClientId'] = 'advec_' . substr(md5(uniqid((string)mt_rand(), true)), 0, 8);
        $data['content_view'] = view('api_client/api_client-form', $data);
        return view('_layout', $data);
    }

    /**
     * Formulário de Edição de Aplicação
     */
    public function editar($id_api_client)
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return redirect()->to('accessdeny');
        }

        $id_api_client = (int)$id_api_client;
        $apiClientModel = new ApiClientModel();
        $cliente = $apiClientModel->find($id_api_client);

        if (!$cliente) {
            session()->setFlashdata('error', 'Aplicação não encontrada.');
            return redirect()->to('api-client');
        }

        $data['cliente'] = $cliente;
        $data['sugestaoClientId'] = $cliente->client_id;
        $data['content_view'] = view('api_client/api_client-form', $data);
        return view('_layout', $data);
    }

    /**
     * Salva ou atualiza aplicação
     */
    public function salvar()
    {
        $data = $this->session();
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to('api-client');
        }

        $id_api_client = (int)$this->request->getPost('id_api_client');
        $isEdit = ($id_api_client > 0);

        if ($isEdit) {
            if (empty($data['sys_action']->update)) {
                return redirect()->to('accessdeny');
            }
        } else {
            if (empty($data['sys_action']->create)) {
                return redirect()->to('accessdeny');
            }
        }

        $nome_aplicacao = trim((string)$this->request->getPost('nome_aplicacao'));
        $client_id      = trim((string)$this->request->getPost('client_id'));
        $tempo_exp      = max(60, (int)$this->request->getPost('tempo_expiracao'));
        $rate_limit     = max(5, (int)$this->request->getPost('limite_requisicoes_minuto'));
        $status         = (int)$this->request->getPost('status');
        $descricao      = trim((string)$this->request->getPost('descricao'));

        if (empty($nome_aplicacao) || empty($client_id)) {
            session()->setFlashdata('error', 'Preencha todos os campos obrigatórios (*).');
            return redirect()->back()->withInput();
        }

        $apiClientModel = new ApiClientModel();

        // Valida duplicidade de client_id
        $existente = $apiClientModel->buscarPorClientId($client_id);
        if ($existente && (!$isEdit || (int)$existente->id_api_client !== $id_api_client)) {
            session()->setFlashdata('error', "O Client ID '{$client_id}' já está em uso por outra aplicação. Escolha outro.");
            return redirect()->back()->withInput();
        }

        $dados = [
            'nome_aplicacao'            => $nome_aplicacao,
            'client_id'                 => $client_id,
            'tempo_expiracao'           => $tempo_exp,
            'limite_requisicoes_minuto' => $rate_limit,
            'status'                    => $status,
            'descricao'                 => $descricao
        ];

        if (!$isEdit) {
            // Gera chaves aleatórias seguras no cadastro
            $dados['client_secret'] = 'sec_' . JwtService::gerarChaveAleatoria(24);
            $dados['jwt_secret']    = JwtService::gerarChaveAleatoria(32);
            $apiClientModel->insert($dados);
            $msg = 'Aplicação de API cadastrada com sucesso! As credenciais foram geradas.';
        } else {
            $apiClientModel->update($id_api_client, $dados);
            $msg = 'Aplicação de API atualizada com sucesso!';
        }

        session()->setFlashdata('success', $msg);
        return redirect()->to('api-client');
    }

    /**
     * AJAX: Rotaciona chaves de segurança (Client Secret e JWT Secret)
     */
    public function rotacionarSecret()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $id_api_client = (int)$this->request->getPost('id_api_client');
        $apiClientModel = new ApiClientModel();
        $novasChaves = $apiClientModel->rotacionarChaves($id_api_client);

        if (!$novasChaves) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Aplicação não encontrada.']);
        }

        return $this->response->setJSON([
            'status'        => 'success',
            'client_secret' => $novasChaves['client_secret'],
            'message'       => 'Novas chaves geradas com sucesso! Atualize as credenciais no seu n8n.'
        ]);
    }

    /**
     * AJAX: Alterna status ativo/inativo
     */
    public function toggleStatus()
    {
        $data = $this->session();
        if (empty($data['sys_action']->update)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acesso negado.']);
        }

        $id_api_client = (int)$this->request->getPost('id_api_client');
        $apiClientModel = new ApiClientModel();
        $novoStatus = $apiClientModel->toggleStatus($id_api_client);

        $label = ($novoStatus === 1) ? 'Ativada' : 'Inativada';
        return $this->response->setJSON([
            'status'      => 'success',
            'novo_status' => $novoStatus,
            'message'     => "Aplicação {$label} com sucesso!"
        ]);
    }

    /**
     * Exclui aplicação
     */
    public function apagar($id_api_client)
    {
        $data = $this->session();
        if (empty($data['sys_action']->delete)) {
            return redirect()->to('accessdeny');
        }

        $id_api_client = (int)$id_api_client;
        $apiClientModel = new ApiClientModel();
        $apiClientModel->delete($id_api_client);

        session()->setFlashdata('success', 'Aplicação excluída com sucesso!');
        return redirect()->to('api-client');
    }
}
