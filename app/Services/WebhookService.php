<?php

namespace App\Services;

use App\Models\WebhookModel;

class WebhookService
{
    /**
     * Higieniza e padroniza o número de telefone no formato internacional (DDI + DDD + Número)
     * Exemplo: "(11) 98765-4321" -> "5511987654321"
     */
    public static function formatarTelefoneWhatsApp($telefone)
    {
        $digitos = preg_replace('/\D/', '', (string)$telefone);

        if (empty($digitos)) {
            return '';
        }

        // Se começar com 0, remove o zero inicial (ex: 011987654321 -> 11987654321)
        if (strpos($digitos, '0') === 0 && strlen($digitos) > 10) {
            $digitos = substr($digitos, 1);
        }

        // Se já começa com DDI 55 e tem tamanho adequado (12 ou 13 dígitos)
        if (strpos($digitos, '55') === 0 && (strlen($digitos) === 12 || strlen($digitos) === 13)) {
            return $digitos;
        }

        // Se tem 10 ou 11 dígitos (DDD + 8 ou 9 dígitos), adiciona DDI 55 do Brasil
        if (strlen($digitos) === 10 || strlen($digitos) === 11) {
            return '55' . $digitos;
        }

        return $digitos;
    }

    /**
     * Dispara mensagem de WhatsApp através do Webhook ativo cadastrado no sistema
     * 
     * Payload OBRIGATÓRIO em formato de Lista/Array:
     * [
     *   {
     *     "instance": "instancia_padrao_do_banco",
     *     "to": "5511999999999",
     *     "message": "Mensagem..."
     *   }
     * ]
     */
    public static function dispararMensagemWhatsApp($telefone, $mensagem, $instanciaCustomizada = null)
    {
        $webhookModel = new WebhookModel();
        $webhook = $webhookModel->getWebhookAtivo();

        if (!$webhook || empty($webhook->url)) {
            log_message('warning', '[WebhookService] Nenhum webhook ativo cadastrado no banco de dados.');
            return [
                'success'   => false,
                'message'   => 'Nenhum webhook ativo cadastrado no sistema.',
                'http_code' => null
            ];
        }

        $numeroFormatado = self::formatarTelefoneWhatsApp($telefone);
        if (empty($numeroFormatado)) {
            return [
                'success'   => false,
                'message'   => 'Número de telefone inválido para envio.',
                'http_code' => null
            ];
        }

        $instancia = !empty($instanciaCustomizada) ? $instanciaCustomizada : $webhook->instancia_padrao;

        // Estrutura em formato de Lista/Array conforme especificação
        $payloadArray = [
            [
                'instance' => (string)$instancia,
                'to'       => (string)$numeroFormatado,
                'message'  => (string)$mensagem
            ]
        ];

        $jsonPayload = json_encode($payloadArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        try {
            $client = \Config\Services::curlrequest([
                'timeout'         => 10,
                'connect_timeout' => 5,
                'http_errors'     => false
            ]);

            $response = $client->request('POST', $webhook->url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json'
                ],
                'body' => $jsonPayload
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();

            $isSuccess = ($statusCode >= 200 && $statusCode < 300);

            if (!$isSuccess) {
                log_message('error', "[WebhookService] Erro ao disparar webhook: HTTP {$statusCode} - {$body}");
            }

            return [
                'success'   => $isSuccess,
                'http_code' => $statusCode,
                'response'  => $body,
                'payload'   => $payloadArray,
                'message'   => $isSuccess ? 'Mensagem enviada com sucesso!' : "Falha no envio (HTTP {$statusCode})."
            ];
        } catch (\Throwable $e) {
            log_message('error', '[WebhookService] Exceção cURL: ' . $e->getMessage());
            return [
                'success'   => false,
                'message'   => 'Erro de conexão com o servidor de webhook: ' . $e->getMessage(),
                'http_code' => 500
            ];
        }
    }

    /**
     * Envia código OTP de 6 dígitos para validação e troca obrigatória de senha
     */
    public static function enviarOtpTrocaSenha($telefone, $otpCode, $nome = '')
    {
        $primeiroNome = !empty($nome) ? ' ' . explode(' ', trim($nome))[0] : '';
        $mensagem = "ADVEC: Olá{$primeiroNome}! Seu código de verificação para definir sua nova senha é: *{$otpCode}*. Este código expira em 10 minutos.";

        return self::dispararMensagemWhatsApp($telefone, $mensagem);
    }
}
