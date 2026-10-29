<?php

namespace App\Libraries;

class JwtService
{
    /**
     * Gera um token JWT assinado com HMAC-SHA256
     *
     * @param array  $payload Dados a serem encapsulados no token
     * @param string $secret  Chave secreta de assinatura
     * @param int    $ttl     Tempo de vida em segundos (padrão 3600 = 1 hora)
     * @return string Token JWT no formato header.payload.signature
     */
    public static function gerarToken(array $payload, string $secret, int $ttl = 3600): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $now = time();
        $payload['iat'] = $now;
        $payload['nbf'] = $now;
        $payload['exp'] = $now + $ttl;
        $payload['iss'] = 'advec-api';

        $base64UrlHeader  = self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));

        $signature = hash_hmac('sha256', "{$base64UrlHeader}.{$base64UrlPayload}", $secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return "{$base64UrlHeader}.{$base64UrlPayload}.{$base64UrlSignature}";
    }

    /**
     * Valida e decodifica um token JWT
     *
     * @param string $jwt    Token JWT
     * @param string $secret Chave secreta de assinatura
     * @return object|null Retorna os dados do payload se válido, ou null se inválido/expirado
     */
    public static function validarToken(string $jwt, string $secret): ?object
    {
        $partes = explode('.', trim($jwt));
        if (count($partes) !== 3) {
            return null;
        }

        [$base64UrlHeader, $base64UrlPayload, $base64UrlSignature] = $partes;

        // Verifica integridade da assinatura
        $signatureEsperada = hash_hmac('sha256', "{$base64UrlHeader}.{$base64UrlPayload}", $secret, true);
        $base64UrlSignatureEsperada = self::base64UrlEncode($signatureEsperada);

        if (!hash_equals($base64UrlSignatureEsperada, $base64UrlSignature)) {
            return null;
        }

        // Decodifica payload
        $payloadJson = self::base64UrlDecode($base64UrlPayload);
        if (!$payloadJson) {
            return null;
        }

        $payload = json_decode($payloadJson);
        if (!$payload) {
            return null;
        }

        // Valida tempo de expiração
        if (isset($payload->exp) && $payload->exp < time()) {
            return null;
        }

        // Valida not-before
        if (isset($payload->nbf) && $payload->nbf > time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Decodifica o payload sem validar assinatura (útil para descobrir o client_id e buscar o secret correspondente)
     */
    public static function decodificarPayloadSemValidacao(string $jwt): ?object
    {
        $partes = explode('.', trim($jwt));
        if (count($partes) !== 3) {
            return null;
        }

        $payloadJson = self::base64UrlDecode($partes[1]);
        if (!$payloadJson) {
            return null;
        }

        return json_decode($payloadJson);
    }

    /**
     * Gera uma chave secreta aleatória segura
     */
    public static function gerarChaveAleatoria(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }

    /**
     * Helper Base64Url Encode (RFC 7515)
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Helper Base64Url Decode (RFC 7515)
     */
    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
