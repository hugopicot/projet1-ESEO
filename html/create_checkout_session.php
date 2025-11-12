<?php
require __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Méthode non autorisée']);
  exit;
}

$amount = 990; // en cents, EUR
$productName = 'Paiement test Nova';

$successUrl = APP_DOMAIN . '/html/patrimoine.php?payment_session_id={CHECKOUT_SESSION_ID}';
$cancelUrl  = APP_DOMAIN . '/html/patrimoine.php?payment_canceled=1';

$params = [
  'mode' => 'payment',
  'success_url' => $successUrl,
  'cancel_url' => $cancelUrl,
  'line_items[0][quantity]' => '1',
  'line_items[0][price_data][currency]' => 'eur',
  'line_items[0][price_data][product_data][name]' => $productName,
  'line_items[0][price_data][unit_amount]' => (string)$amount,
];

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Authorization: Bearer ' . STRIPE_SECRET_KEY,
  'Content-Type: application/x-www-form-urlencoded',
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
if (defined('CURL_SSLVERSION_TLSv1_2')) {
  curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
}
$caBundle = realpath(__DIR__ . '/../config/cacert.pem');
if ($caBundle && is_file($caBundle)) {
  curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
}
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$body = curl_exec($ch);
$err  = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
curl_close($ch);

$json = null;
if (is_string($body)) { $json = json_decode($body, true); }

if ($status < 200 || $status >= 300 || !is_array($json)) {
  http_response_code(500);
  $msg = '';
  if (is_array($json) && isset($json['error']['message'])) { $msg = (string)$json['error']['message']; }
  if ($msg === '') { $msg = $err ?: 'invalid_response'; }
  echo json_encode(['error' => 'Stripe API error', 'details' => $msg, 'status' => $status]);
  exit;
}

$checkoutUrl = isset($json['url']) ? (string)$json['url'] : '';
if ($checkoutUrl === '') {
  http_response_code(500);
  echo json_encode(['error' => 'URL Checkout manquante']);
  exit;
}

echo json_encode(['url' => $checkoutUrl]);
