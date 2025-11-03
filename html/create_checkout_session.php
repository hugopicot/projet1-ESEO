<?php
// Autoload Stripe si disponible (Composer), sinon fallback HTTP
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}
require __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if (!STRIPE_SECRET_KEY || strpos(STRIPE_SECRET_KEY, 'sk_test_') !== 0) {
    http_response_code(500);
    echo json_encode(['error' => 'Clé Stripe test manquante. Renseignez STRIPE_SECRET_KEY.']);
    exit;
}

$successUrl = APP_DOMAIN . '/html/checkout_success.php?session_id={CHECKOUT_SESSION_ID}';
$cancelUrl  = APP_DOMAIN . '/html/checkout_cancel.php';

try {
    if (class_exists('Stripe\\StripeClient')) {
        // Utilisation SDK Stripe si installé
        $stripe = new \Stripe\StripeClient(STRIPE_SECRET_KEY);
        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Abonnement Nova'],
                    'unit_amount' => 990,
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'locale'      => 'fr',
        ]);
        echo json_encode(['url' => $session->url]);
    } else {
        // Fallback HTTP direct vers l’API Stripe
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        $payload = http_build_query([
            'mode' => 'payment',
            'payment_method_types[]' => 'card',
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'locale'      => 'fr',
            // line_items[0][...] syntax pour price_data inline
            'line_items[0][quantity]' => 1,
            'line_items[0][price_data][currency]' => 'eur',
            'line_items[0][price_data][product_data][name]' => 'Abonnement Nova',
            'line_items[0][price_data][unit_amount]' => 990,
        ]);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . STRIPE_SECRET_KEY],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $resp = curl_exec($ch);
        if ($resp === false) { throw new \RuntimeException('Erreur réseau Stripe'); }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $json = json_decode($resp, true);
        if ($code >= 400) { throw new \RuntimeException($json['error']['message'] ?? 'Erreur Stripe'); }
        echo json_encode(['url' => $json['url'] ?? '']);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}