<?php
require __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

// Simulation simple d'une session Checkout: on génère un ID factice
$fakeSessionId = 'sess_' . bin2hex(random_bytes(6));
$successUrl = APP_DOMAIN . '/html/app.php?session_id=' . urlencode($fakeSessionId);

echo json_encode(['url' => $successUrl]);