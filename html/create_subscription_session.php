<?php
// Endpoint pour créer une session Stripe Checkout en mode abonnement (test)
// Supporte SDK Stripe si installé, sinon fallback vers requête HTTP directe.

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}
require __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

if (!STRIPE_SECRET_KEY || strpos(STRIPE_SECRET_KEY, 'sk_test_') !== 0) {
    http_response_code(500);
    echo json_encode(['error' => 'Clé Stripe test manquante. Renseignez STRIPE_SECRET_KEY.']);
    exit;
}

$plan = $_POST['plan'] ?? '';
if (!in_array($plan, ['starter', 'pro'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Plan invalide.']);
    exit;
}

// Montants en centimes pour EUR
$unitAmount = $plan === 'starter' ? 2700 : 4900;
$productName = $plan === 'starter' ? 'Abonnement Nova Starter' : 'Abonnement Nova Pro';

$successUrl = APP_DOMAIN . '/html/app.php?session_id={CHECKOUT_SESSION_ID}&plan=' . urlencode($plan);
$cancelUrl  = APP_DOMAIN . '/html/checkout_cancel.php?plan=' . urlencode($plan);

try {
    if (class_exists('Stripe\\StripeClient')) {
        $stripe = new \Stripe\StripeClient(STRIPE_SECRET_KEY);
        $session = $stripe->checkout->sessions->create([
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => $productName],
                    'unit_amount' => $unitAmount,
                    'recurring' => [
                        'interval' => 'month',
                    ],
                ],
                'quantity' => 1,
            ]],
            'subscription_data' => [
                'trial_period_days' => 30,
            ],
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'locale'      => 'fr',
        ]);
        echo json_encode(['url' => $session->url]);
    } else {
        // Fallback HTTP direct vers l’API Stripe
        $payload = http_build_query([
            'mode' => 'subscription',
            'payment_method_types[]' => 'card',
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'locale'      => 'fr',
            'subscription_data[trial_period_days]' => 30,
            // line_items[0] avec price_data récurrent
            'line_items[0][quantity]' => 1,
            'line_items[0][price_data][currency]' => 'eur',
            'line_items[0][price_data][product_data][name]' => $productName,
            'line_items[0][price_data][unit_amount]' => $unitAmount,
            'line_items[0][price_data][recurring][interval]' => 'month',
        ]);

        // Optionnel: chemin du bundle CA via variables d’environnement ou fichier local
        $caBundleEnv = getenv('SSL_CERT_FILE') ?: getenv('CURL_CA_BUNDLE');
        $localCa = dirname(__DIR__) . '/config/cacert.pem';
        $caBundle = (is_string($caBundleEnv) && file_exists($caBundleEnv)) ? $caBundleEnv : (file_exists($localCa) ? $localCa : null);
        $caReadable = $caBundle && is_readable($caBundle);
        $insecure = in_array(strtolower(getenv('STRIPE_INSECURE_SSL') ?: ''), ['1', 'true', 'yes']);

        if (function_exists('curl_init')) {
            $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
            $curlOptions = [
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . STRIPE_SECRET_KEY],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                // SSL
                CURLOPT_SSL_VERIFYPEER => !$insecure,
                CURLOPT_SSL_VERIFYHOST => $insecure ? 0 : 2,
            ];
            // Ajouter CA uniquement si chemin valide et lisible
            if ($caReadable) { $curlOptions[CURLOPT_CAINFO] = $caBundle; }
            curl_setopt_array($ch, $curlOptions);
            $resp = curl_exec($ch);
            if ($resp === false) {
                $err = curl_error($ch);
                curl_close($ch);
                $caNote = $caReadable ? (' (CA=' . $caBundle . ')') : ($caBundle ? (' (CA non lisible: ' . $caBundle . ')') : '');
                throw new \RuntimeException('Erreur réseau Stripe: ' . ($err ?: 'inconnue') . $caNote . ($insecure ? ' [SSL vérification désactivée]' : ''));
            }
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $json = json_decode($resp, true);
            if ($code >= 400) { throw new \RuntimeException($json['error']['message'] ?? 'Erreur Stripe (' . $code . ')'); }
            echo json_encode(['url' => $json['url'] ?? '']);
        } else {
            // Fallback sans cURL: stream context
            $sslOpts = [
                'verify_peer' => !$insecure,
                'verify_peer_name' => !$insecure,
            ];
            if ($caReadable) { $sslOpts['cafile'] = $caBundle; }

            $ctx = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Authorization: Bearer " . STRIPE_SECRET_KEY . "\r\nContent-Type: application/x-www-form-urlencoded",
                    'content' => $payload,
                    'timeout' => 15,
                ],
                'ssl' => $sslOpts,
            ]);
            $resp = @file_get_contents('https://api.stripe.com/v1/checkout/sessions', false, $ctx);
            if ($resp === false) {
                $last = error_get_last();
                $caNote = $caReadable ? (' (CA=' . $caBundle . ')') : ($caBundle ? (' (CA non lisible: ' . $caBundle . ')') : '');
                throw new \RuntimeException('Erreur réseau Stripe (stream): ' . ($last['message'] ?? 'inconnue') . $caNote . ($insecure ? ' [SSL vérification désactivée]' : ''));
            }
            $json = json_decode($resp, true);
            if (isset($json['error'])) { throw new \RuntimeException($json['error']['message'] ?? 'Erreur Stripe'); }
            echo json_encode(['url' => $json['url'] ?? '']);
        }
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}