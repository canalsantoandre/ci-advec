<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ApiRateLimitFilter implements FilterInterface
{
    /**
     * Aplica proteção de Rate Limit para prevenir força bruta e sobrecarga na API
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $throttler = Services::throttler();
        $ip = $request->getIPAddress();
        $uri = $request->getUri()->getPath();

        // 1. Rota de Autenticação / Emissão de Token: Proteção Anti-Força-Bruta Estrita
        if (strpos($uri, 'api/v1/auth/token') !== false) {
            $chaveRateLimit = md5('api_auth_ip_' . $ip);
            // Máximo de 10 tentativas de login/token por minuto por IP
            $permitido = $throttler->check($chaveRateLimit, 10, MINUTE);

            if ($permitido === false) {
                $segundosRestantes = 60;
                $response = Services::response();
                return $response->setStatusCode(429)
                    ->setHeader('Retry-After', (string)$segundosRestantes)
                    ->setJSON([
                        'status'      => 'error',
                        'code'        => 'RATE_LIMIT_EXCEEDED',
                        'message'     => 'Muitas tentativas de autenticação. Limite de segurança excedido. Tente novamente em 1 minuto.',
                        'retry_after' => $segundosRestantes
                    ]);
            }

            return;
        }

        // 2. Rotas Protegidas da API: Rate Limit geral por IP ou por cliente
        $limitePorMinuto = !empty($arguments[0]) ? (int)$arguments[0] : 60;
        $chaveRateLimit = md5('api_req_' . $ip);

        $permitido = $throttler->check($chaveRateLimit, $limitePorMinuto, MINUTE);

        if ($permitido === false) {
            $segundosRestantes = 60;
            $response = Services::response();
            return $response->setStatusCode(429)
                ->setHeader('Retry-After', (string)$segundosRestantes)
                ->setHeader('X-RateLimit-Limit', (string)$limitePorMinuto)
                ->setHeader('X-RateLimit-Remaining', '0')
                ->setJSON([
                    'status'      => 'error',
                    'code'        => 'RATE_LIMIT_EXCEEDED',
                    'message'     => 'Limite de requisições excedido. Por favor, aguarde alguns segundos.',
                    'retry_after' => $segundosRestantes
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Headers de segurança para APIs REST
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'DENY');
        return $response;
    }
}
