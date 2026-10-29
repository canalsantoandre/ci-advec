<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use App\Libraries\JwtService;
use App\Models\ApiClientModel;

class JwtApiFilter implements FilterInterface
{
    /**
     * Valida autenticação por Bearer JWT nas rotas da API
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getServer('HTTP_AUTHORIZATION') 
            ?: $request->getHeaderLine('Authorization')
            ?: $request->getServer('REDIRECT_HTTP_AUTHORIZATION');

        if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/i', $authHeader, $matches)) {
            $response = Services::response();
            return $response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'code'    => 'AUTH_HEADER_MISSING',
                'message' => 'Acesso negado. Cabeçalho Authorization: Bearer <TOKEN> ausente ou mal formatado.'
            ]);
        }

        $jwt = trim($matches[1]);

        // 1. Decodifica o payload sem validação para identificar o client_id emissor
        $payloadParcial = JwtService::decodificarPayloadSemValidacao($jwt);
        if (!$payloadParcial || empty($payloadParcial->sub)) {
            $response = Services::response();
            return $response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'code'    => 'TOKEN_MALFORMED',
                'message' => 'Token JWT malformado ou identificador de cliente ausente.'
            ]);
        }

        $clientId = (string)$payloadParcial->sub;
        $apiClientModel = new ApiClientModel();
        $client = $apiClientModel->buscarAtivoPorClientId($clientId);

        if (!$client) {
            $response = Services::response();
            return $response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'code'    => 'CLIENT_INACTIVE_OR_NOT_FOUND',
                'message' => 'Aplicação de API inativa ou não encontrada. Verifique as configurações no painel ADVEC.'
            ]);
        }

        // 2. Valida a assinatura criptográfica e tempo de expiração do JWT
        $payloadValido = JwtService::validarToken($jwt, $client->jwt_secret);
        if (!$payloadValido) {
            $response = Services::response();
            return $response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'code'    => 'TOKEN_INVALID_OR_EXPIRED',
                'message' => 'Token JWT expirado ou inválido. Solicite um novo token em /api/v1/auth/token.'
            ]);
        }

        // 3. Aplica Rate Limit específico configurado para este cliente
        $limiteCliente = (int)($client->limite_requisicoes_minuto ?? 60);
        $throttler = Services::throttler();
        $chaveClient = md5('api_client_throttle_' . $client->client_id);

        if (!$throttler->check($chaveClient, $limiteCliente, MINUTE)) {
            $response = Services::response();
            return $response->setStatusCode(429)
                ->setHeader('Retry-After', '60')
                ->setHeader('X-RateLimit-Limit', (string)$limiteCliente)
                ->setHeader('X-RateLimit-Remaining', '0')
                ->setJSON([
                    'status'      => 'error',
                    'code'        => 'RATE_LIMIT_EXCEEDED',
                    'message'     => "Limite de requisições excedido para a aplicação '{$client->nome_aplicacao}' ({$limiteCliente} req/min). Aguarde alguns segundos.",
                    'retry_after' => 60
                ]);
        }

        // Registra o uso da aplicação
        $apiClientModel->registrarAcesso((int)$client->id_api_client);

        // Compartilha o cliente validado na requisição
        $GLOBALS['current_api_client'] = $client;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
