<?php
// Client Powens minimal: échange code OAuth2 -> tokens, récupération comptes
// Les endpoints peuvent varier par environnement; utilisez POWENS_API_BASE via env.

if (!defined('POWENS_API_BASE')) {
    // Par défaut, API publique Powens. Override via env POWENS_API_BASE si nécessaire.
    define('POWENS_API_BASE', getenv('POWENS_API_BASE') ?: 'https://api.powens.com');
}

/**
 * Effectue une requête HTTP via cURL.
 */
function powens_http(string $method, string $url, array $headers = [], $body = null): array {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    // User-Agent pour diagnostics côté API
    curl_setopt($ch, CURLOPT_USERAGENT, 'NovaPowensClient/1.0');
    // Sécurité SSL basique; en dev, vous pouvez assouplir si nécessaire
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    // Force TLS v1.2 si disponible (compatibilité API Powens)
    if (defined('CURL_SSLVERSION_TLSv1_2')) {
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    }
    // Utilise un bundle CA local si disponible (Windows/dev)
    $caBundle = realpath(__DIR__ . '/../config/cacert.pem');
    if ($caBundle && is_file($caBundle)) {
        curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
    }
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $response = curl_exec($ch);
    $err = curl_error($ch);
    $errno = curl_errno($ch);
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    return [
        'status' => (int)$status,
        'error'  => $err,
        'errno'  => (int)$errno,
        'body'   => $response,
    ];
}

/**
 * Échange un code d'autorisation contre des tokens OAuth2.
 */
function powens_exchange_code(string $code, string $redirectUri, string $clientId, string $clientSecret): array {
    $url = rtrim(POWENS_API_BASE, '/') . '/oauth2/token';
    $payload = http_build_query([
        'grant_type'   => 'authorization_code',
        'client_id'    => $clientId,
        'client_secret'=> $clientSecret,
        'code'         => $code,
        'redirect_uri' => $redirectUri,
    ]);
    $res = powens_http('POST', $url, ['Content-Type: application/x-www-form-urlencoded'], $payload);
    $data = null;
    if (is_string($res['body'])) { $data = json_decode($res['body'], true); }
    return [
        'ok'    => ($res['status'] >= 200 && $res['status'] < 300) && is_array($data),
        'data'  => is_array($data) ? $data : null,
        'status'=> $res['status'],
        'error' => $res['error'] ?: ($data['error'] ?? null),
        'errno' => $res['errno'],
        'raw'   => $res['body'],
    ];
}

/**
 * Récupère les comptes de l'utilisateur (implémentation indicative, peut varier).
 * Essayez /v2/ routes si nécessaire; ici on tente un fallback commun.
 */
function powens_get_accounts(string $accessToken): array {
    // Plusieurs APIs possibles; essaye endpoints courants
    $candidates = [
        rtrim(POWENS_API_BASE, '/') . '/v2/users/me/accounts',
        rtrim(POWENS_API_BASE, '/') . '/v2/accounts',
        rtrim(POWENS_API_BASE, '/') . '/accounts',
    ];
    foreach ($candidates as $url) {
        $res = powens_http('GET', $url, ['Authorization: Bearer ' . $accessToken]);
        $data = null;
        if (is_string($res['body'])) { $data = json_decode($res['body'], true); }
        if (($res['status'] >= 200 && $res['status'] < 300) && is_array($data)) {
            return [ 'ok' => true, 'data' => $data, 'status' => $res['status'], 'error' => null, 'raw' => $res['body'] ];
        }
    }
    return [ 'ok' => false, 'data' => null, 'status' => 0, 'error' => 'No accounts endpoint succeeded', 'raw' => null ];
}

/**
 * Utilitaire: extrait champs standard d'un compte si présents.
 */
function powens_account_fields(array $account): array {
    $id       = (string)($account['id'] ?? $account['account_id'] ?? '');
    $name     = (string)($account['name'] ?? $account['label'] ?? '');
    $type     = (string)($account['type'] ?? $account['category'] ?? '');
    $currency = (string)($account['currency'] ?? 'EUR');
    $balance  = null;
    if (isset($account['balance'])) {
        $balance = (float)$account['balance'];
    } elseif (isset($account['amount'])) {
        $balance = (float)$account['amount'];
    } elseif (isset($account['valuation'])) {
        $balance = (float)$account['valuation'];
    }
    return [
        'id' => $id,
        'name' => $name,
        'type' => $type,
        'currency' => $currency,
        'balance' => $balance,
    ];
}

// Pas de sortie HTML
?>