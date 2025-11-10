<?php
global $pdo;
require_once '../config/config.php';
$id = $_GET['id'] ?? null;
if (!$id) exit("ID manquant");

$pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);

header("Location: ../html/admin_panel.php");
exit;
