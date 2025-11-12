<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/market_data.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ./auth.php');
    exit;
}

// Support JSON body or form-encoded
$input = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = (string)file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) { $input = $json; }
    else { $input = $_POST; }
} else { $input = $_GET; }

$category = trim((string)($input['category'] ?? ''));
$type     = trim((string)($input['type'] ?? 'equity')); // 'equity' | 'crypto'
$external = trim((string)($input['external_id'] ?? ''));
$name     = trim((string)($input['name'] ?? ''));
$quantity = (float)($input['quantity'] ?? 0);
$purchase = isset($input['purchase_price']) ? (float)$input['purchase_price'] : null;

if ($category === '' || $external === '' || $quantity <= 0) {
    $_SESSION['error'] = "Informations manquantes (catégorie, identifiant externe, quantité).";
    header('Location: ./add_assets.php');
    exit;
}

// Obtenir le prix courant
$price = null; $currency = 'EUR'; $dataSource = null;
if ($type === 'crypto') {
    $q = crypto_price_aggregator($external, '', 'eur');
    if (!empty($q['ok'])) { $price = (float)$q['price']; $currency = strtoupper((string)($q['currency'] ?? 'EUR')); $dataSource = (string)($q['provider'] ?? 'aggregator'); }
} else {
    $q = yahoo_quote($external);
    if ($q['ok']) { $price = (float)$q['price']; $currency = (string)$q['currency']; $name = $name ?: (string)$q['name']; $dataSource = 'yahoo'; }
}
// Si le prix courant est indisponible, ne pas échouer: utiliser une valorisation provisoire
$noteFallback = null;
if ($price === null) {
    // Valorisation provisoire basée sur le prix d'achat si fourni, sinon 0
    $currency = 'EUR';
    $dataSource = $dataSource ?: 'manual';
    $noteFallback = ($purchase !== null && $purchase > 0)
        ? "Prix courant indisponible; valorisation provisoire basée sur votre prix d'achat."
        : "Prix courant indisponible; actif ajouté, valorisation mise à jour automatiquement.";
}

// Calcule la valorisation
$valuation_eur = null;
if ($price !== null) {
    $valuation = $price * $quantity; // devise native
    $valuation_eur = $valuation;
    if ($type !== 'crypto' && strtoupper($currency) !== 'EUR') {
        // Convertit en EUR si nécessaire (crypto est déjà en EUR)
        $valuation_eur = fx_convert_amount($valuation, strtoupper($currency), 'EUR');
    }
} else {
    // Fallback: prix d'achat en EUR si disponible, sinon 0
    $valuation_eur = ($purchase !== null && $purchase > 0) ? ($purchase * $quantity) : 0.0;
}

try {
    // positions_manual upsert
    $pdo->exec("CREATE TABLE IF NOT EXISTS positions_manual (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        categorie ENUM('actions_fonds','crypto','immobilier','assurance_vie','autres') NOT NULL,
        external_id VARCHAR(128) NOT NULL,
        name VARCHAR(255) DEFAULT NULL,
        quantity DECIMAL(20,8) NOT NULL DEFAULT 0,
        purchase_price DECIMAL(20,8) DEFAULT NULL,
        currency CHAR(3) NOT NULL DEFAULT 'EUR',
        data_source VARCHAR(32) DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_user_cat_external (user_id, categorie, external_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $stmt = $pdo->prepare("INSERT INTO positions_manual (user_id, categorie, external_id, name, quantity, purchase_price, currency, data_source)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                           ON DUPLICATE KEY UPDATE
                             name = VALUES(name),
                             quantity = quantity + VALUES(quantity),
                             purchase_price = CASE
                               WHEN VALUES(purchase_price) IS NULL OR VALUES(purchase_price) <= 0 THEN purchase_price
                               WHEN purchase_price IS NULL OR purchase_price <= 0 THEN VALUES(purchase_price)
                               ELSE (purchase_price * quantity + VALUES(purchase_price) * VALUES(quantity)) / (quantity + VALUES(quantity))
                             END,
                             currency = VALUES(currency),
                             data_source = VALUES(data_source),
                             updated_at = NOW()");
    $stmt->execute([$_SESSION['user_id'], $category, $external, $name, $quantity, $purchase, $currency, $dataSource]);

    // Récupère la quantité totale et le prix d'achat moyen après cumul
    $pm = $pdo->prepare("SELECT quantity, purchase_price FROM positions_manual WHERE user_id = ? AND categorie = ? AND external_id = ? LIMIT 1");
    $pm->execute([$_SESSION['user_id'], $category, $external]);
    $pos = $pm->fetch(PDO::FETCH_ASSOC) ?: ['quantity' => $quantity, 'purchase_price' => $purchase];
    $totalQty = isset($pos['quantity']) ? (float)$pos['quantity'] : $quantity;
    $avgPurchase = isset($pos['purchase_price']) ? (float)$pos['purchase_price'] : ($purchase ?? 0.0);

    // Essaie d'estimer une valorisation "immédiate" correcte pour le compte
    $immediate_eur = 0.0;
    if ($price !== null) {
        // Prix courant disponible: calcule à partir de la quantité totale
        $valNative = $price * $totalQty;
        $immediate_eur = $valNative;
        if ($type !== 'crypto' && strtoupper($currency) !== 'EUR') {
            $immediate_eur = fx_convert_amount($valNative, strtoupper($currency), 'EUR');
        }
    } else {
        // Fallback: utilise dernier prix unitaire connu si disponible, sinon prix d'achat moyen
        $prev = $pdo->prepare("SELECT balance_eur FROM compte WHERE user_id = ? AND source = 'manual' AND external_id = ? LIMIT 1");
        $prev->execute([$_SESSION['user_id'], $external]);
        $prevBal = $prev->fetch(PDO::FETCH_ASSOC);
        $prevBalEur = isset($prevBal['balance_eur']) ? (float)$prevBal['balance_eur'] : 0.0;
        $prevQty = max($totalQty - $quantity, 0.0);
        $lastUnitEur = ($prevQty > 0 && $prevBalEur > 0) ? ($prevBalEur / $prevQty) : 0.0;
        if ($lastUnitEur > 0) {
            $immediate_eur = $lastUnitEur * $totalQty;
        } else {
            $immediate_eur = ($avgPurchase > 0) ? ($avgPurchase * $totalQty) : 0.0;
        }
    }

    // compte upsert
    $stmt2 = $pdo->prepare("INSERT INTO compte (user_id, source, external_id, powens_account_pk, categorie, subtype, libelle, devise, balance, balance_eur, status, created_at, updated_at)
                            VALUES (?, 'manual', ?, NULL, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
                            ON DUPLICATE KEY UPDATE categorie = VALUES(categorie), subtype = VALUES(subtype), libelle = VALUES(libelle), devise = VALUES(devise), balance = VALUES(balance), balance_eur = VALUES(balance_eur), status = 'active', updated_at = NOW()");
    $subtype = ($type === 'crypto') ? 'crypto' : 'equity';
    $stmt2->execute([$_SESSION['user_id'], $external, $category, $subtype, $name, $currency, $immediate_eur, $immediate_eur]);

    // Recalcule immédiatement les valorisations pour refléter les ajouts côté portefeuille
    try { refresh_manual_assets($pdo, (int)$_SESSION['user_id']); } catch (Throwable $e) { /* ignore */ }

    $_SESSION['success'] = $noteFallback ? ('Actif ajouté. ' . $noteFallback) : 'Actif ajouté avec succès.';
    header('Location: ./patrimoine.php');
    exit;
} catch (Throwable $e) {
    $_SESSION['error'] = "Échec de l'ajout de l'actif.";
    header('Location: ./patrimoine.php');
    exit;
}

?>