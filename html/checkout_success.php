<?php
require __DIR__ . '/../config/config.php';
$sessionId = $_GET['session_id'] ?? '';
?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <title>Paiement réussi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>body{font-family:Segoe UI,system-ui,Arial,sans-serif;background:#0f1115;color:#e6e6e6;padding:40px}</style>
</head>
<body>
  <h1>Paiement réussi 🎉</h1>
  <p>Merci ! Votre paiement a été confirmé en mode test.</p>
  <?php if ($sessionId): ?>
    <p>Session: <code><?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?></code></p>
  <?php endif; ?>
  <p><a href="/html/checkout.php">Revenir</a></p>
</body>
</html>