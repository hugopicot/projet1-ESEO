<?php
// Autoload Stripe si disponible (Composer)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}
require __DIR__ . '/../config/config.php';

if (class_exists('Stripe\\Webhook')) {
    use Stripe\Webhook;
    use Stripe\Exception\SignatureVerificationException;
}

header('Content-Type: application/json');

// Si un secret de webhook est défini, on vérifie la signature
$payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    if (STRIPE_WEBHOOK_SECRET && class_exists('Stripe\\Webhook')) {
        $event = Webhook::constructEvent($payload, $sigHeader, STRIPE_WEBHOOK_SECRET);
    } else {
        // En local sans secret ou sans SDK: décodage simple
        $event = json_decode($payload, true);
        if (!$event) { throw new \RuntimeException('Payload invalide'); }
    }
} catch (\Throwable $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// Traitement minimal de quelques événements utiles
$type = is_array($event) ? ($event['type'] ?? '') : (is_object($event) && isset($event->type) ? $event->type : '');

switch ($type) {
    case 'checkout.session.completed':
        // TODO: marquer la commande comme payée
        break;
    case 'payment_intent.succeeded':
        // TODO: mise à jour éventuelle
        break;
    default:
        // autres événements ignorés
}

http_response_code(200);
echo json_encode(['status' => 'ok']);