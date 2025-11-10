<?php
// Version simplifiée: accepte le webhook et répond "ok".
header('Content-Type: application/json');
$payload = @file_get_contents('php://input');
$event = json_decode($payload, true);
http_response_code(200);
echo json_encode(['status' => 'ok']);