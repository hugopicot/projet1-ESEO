<?php
global $pdo;
require_once "./config.php";

$type = $_POST['type'] ?? null;
if (!$type) exit;

$stmt = $pdo->prepare("
    INSERT INTO consentement (type_consentement, granted_at)
    VALUES (?, NOW())
");

$stmt->execute([$type]);

echo "OK";
