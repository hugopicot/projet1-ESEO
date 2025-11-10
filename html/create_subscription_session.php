<?php
require __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$plan = $_POST['plan'] ?? '';
if (!in_array($plan, ['starter', 'pro'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Plan invalide.']);
    exit;
}

// Simulation simple d'une session Stripe: ID factice et URL de succès
$fakeSessionId = 'sess_' . bin2hex(random_bytes(6));
$successUrl = APP_DOMAIN . '/html/app.php?session_id=' . urlencode($fakeSessionId) . '&plan=' . urlencode($plan);

echo json_encode(['url' => $successUrl]);