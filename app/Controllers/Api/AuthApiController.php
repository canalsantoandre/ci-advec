<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ApiClientModel;
use App\Libraries\JwtService;

class AuthApiController extends BaseController
{
    /**
     * Emite ou renova token JWT para clientes autorizados (ex: n8n)
     * POST /api/v1/auth/token
     */
    public function token()
    {
        // Aceita dados via JSON ou x-www-form-urlencoded
        $input = $this->request->getJSON(true);
        if (empty($input)) {
            $input = $this->request->getPost();
        }

        $clientId     = trim((string)($input['client_id'] ?? ''));
        $clientSecret = trim((string)($input['client_secret'] ?? ''));

        if (empty($clientId) || empty($clientSecret)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'code'    => 'INVALID_REQUEST',
                'message' => 'Por favor, informe os campos client_id e client_secret.'
            ]);
        }

        $apiClientModel = new ApiClientModel();
        $client = $apiClientModel->buscarPorClientId($clientId);

        if (!$client) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'code'    => 'INVALID_CLIENT',
                'message' => 'Credenciais inválidas: cliente não encontrado.'
            ]);
        }

        if ((int)$client->status !== 1) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'code'    => 'CLIENT_INACTIVE',
                'message' => 'Esta aplicação de API está inativa no painel da ADVEC.'
            ]);
        }

        if (!hash_equals($client->client_secret, $clientSecret)) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'code'    => 'INVALID_CREDENTIALS',
                'message' => 'Credenciais inválidas: client_secret incorreto.'
            ]);
        }

        $ttl = (int)($client->tempo_expiracao ?? 3600);
        if ($ttl <= 0) {
            $ttl = 3600;
        }

        $payload = [
            'sub'      => $client->client_id,
            'app_id'   => $client->id_api_client,
            'app_name' => $client->nome_aplicacao
        ];

        $jwt = JwtService::gerarToken($payload, $client->jwt_secret, $ttl);

        // Registra acesso da aplicação
        $apiClientModel->registrarAcesso((int)$client->id_api_client);

        return $this->response->setStatusCode(200)->setJSON([
            'access_token' => $jwt,
            'token_type'   => 'Bearer',
            'expires_in'   => $ttl,
            'client_id'    => $client->client_id,
            'app_name'     => $client->nome_aplicacao
        ]);
    }
}
