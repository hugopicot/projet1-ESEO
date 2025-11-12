<?php
if (!function_exists('http_json')) {
    function http_json(string $url, int $timeout = 10): array {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'NovaMarketData/1.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        if (defined('CURL_SSLVERSION_TLSv1_2')) {
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        }
        $caBundle = realpath(__DIR__ . '/../config/cacert.pem');
        if ($caBundle && is_file($caBundle)) { curl_setopt($ch, CURLOPT_CAINFO, $caBundle); }
        $body = curl_exec($ch);
        $err  = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        $data = null;
        if (is_string($body)) { $data = json_decode($body, true); }
        return ['status' => (int)$status, 'error' => $err, 'data' => is_array($data) ? $data : null, 'raw' => $body];
    }
}

if (!function_exists('http_json_retry')) {
    function http_json_retry(string $url, int $timeout = 10, int $attempts = 3, int $initialDelayMs = 200): array {
        $last = ['status' => 0, 'error' => 'retry_not_attempted', 'data' => null, 'raw' => null];
        $delay = $initialDelayMs;
        for ($i = 0; $i < max(1, $attempts); $i++) {
            $res = http_json($url, $timeout);
            $last = $res;
            if ($res['status'] >= 200 && $res['status'] < 300 && (isset($res['data']) || isset($res['raw']))) {
                break;
            }
            if ($i < $attempts - 1) { usleep($delay * 1000); $delay = min($delay * 2, 1500); }
        }
        return $last;
    }
}

function yahoo_search(string $q, string $lang = 'fr', string $region = 'FR'): array {
    return ['ok' => false, 'items' => [], 'status' => 501, 'error' => 'disabled'];
    $url = 'https://query1.finance.yahoo.com/v1/finance/search?lang=' . urlencode($lang) . '&region=' . urlencode($region) . '&quotesCount=20&newsCount=0&enableFuzzyQuery=false&query=' . urlencode($q);
    $res = http_json($url, 10);
    $out = [];
    if ($res['status'] >= 200 && $res['status'] < 300 && isset($res['data']['quotes'])) {
        foreach ($res['data']['quotes'] as $quote) {
            $symbol = (string)($quote['symbol'] ?? '');
            $name   = (string)($quote['shortname'] ?? $quote['longname'] ?? $quote['name'] ?? '');
            $type   = strtoupper((string)($quote['quoteType'] ?? ''));
            if ($symbol !== '' && ($type === 'EQUITY' || $type === 'ETF')) {
                $out[] = ['symbol' => $symbol, 'name' => $name, 'type' => strtolower($type)];
            }
        }
    }

    if (empty($out)) {
        $url2 = 'https://query1.finance.yahoo.com/v1/finance/search?lang=en&region=US&quotesCount=20&newsCount=0&enableFuzzyQuery=false&query=' . urlencode($q);
        $res2 = http_json($url2, 10);
        if ($res2['status'] >= 200 && $res2['status'] < 300 && isset($res2['data']['quotes'])) {
            foreach ($res2['data']['quotes'] as $quote) {
                $symbol = (string)($quote['symbol'] ?? '');
                $name   = (string)($quote['shortname'] ?? $quote['longname'] ?? $quote['name'] ?? '');
                $type   = strtoupper((string)($quote['quoteType'] ?? ''));
                if ($symbol !== '' && ($type === 'EQUITY' || $type === 'ETF')) {
                    $out[] = ['symbol' => $symbol, 'name' => $name, 'type' => strtolower($type)];
                }
            }
        }
    }

    if (empty($out)) {
        $autoUrl = 'https://autoc.finance.yahoo.com/autoc?query=' . urlencode($q) . '&lang=en&region=US';
        $auto = http_json($autoUrl, 10);
        if ($auto['status'] >= 200 && $auto['status'] < 300 && isset($auto['data']['ResultSet']['Result'])) {
            foreach ($auto['data']['ResultSet']['Result'] as $it) {
                $symbol = (string)($it['symbol'] ?? '');
                $name   = (string)($it['name'] ?? '');
                $type   = strtoupper((string)($it['type'] ?? ''));
                if ($symbol !== '' && ($type === 'S' || $type === 'EQUITY' || $type === 'ETF')) {
                    $out[] = ['symbol' => $symbol, 'name' => $name, 'type' => ($type === 'ETF' ? 'etf' : 'equity')];
                }
            }
        }
    }

    return ['ok' => !empty($out), 'items' => $out, 'status' => $res['status'], 'error' => $res['error']];
}

function yahoo_quote(string $symbol): array {
    return ['ok' => false, 'symbol' => $symbol];
    $url = 'https://query1.finance.yahoo.com/v7/finance/quote?symbols=' . urlencode($symbol);
    $res = http_json($url, 10);
    if ($res['status'] >= 200 && $res['status'] < 300 && isset($res['data']['quoteResponse']['result'][0])) {
        $r = $res['data']['quoteResponse']['result'][0];
        $price = isset($r['regularMarketPrice']) ? (float)$r['regularMarketPrice'] : null;
        $currency = (string)($r['currency'] ?? 'EUR');
        $name = (string)($r['longName'] ?? $r['shortName'] ?? '');
        return ['ok' => ($price !== null), 'symbol' => $symbol, 'name' => $name, 'price' => $price, 'currency' => $currency];
    }
    return ['ok' => false, 'symbol' => $symbol];
}

function fx_rate(string $from, string $to = 'EUR'): array {
    $from = strtoupper($from); $to = strtoupper($to);
    if ($from === '' || $to === '') { return ['ok' => false, 'error' => 'invalid_currency']; }
    if ($from === $to) { return ['ok' => true, 'rate' => 1.0]; }
    $url = 'https://api.exchangerate.host/latest?base=' . urlencode($from) . '&symbols=' . urlencode($to);
    $res = http_json($url, 8);
    if ($res['status'] >= 200 && $res['status'] < 300 && isset($res['data']['rates'][$to])) {
        return ['ok' => true, 'rate' => (float)$res['data']['rates'][$to]];
    }
    return ['ok' => false, 'error' => 'unavailable'];
}

function fx_convert_amount(float $amount, string $from, string $to = 'EUR'): float {
    $fx = fx_rate($from, $to);
    if (!empty($fx['ok'])) { return $amount * (float)$fx['rate']; }
    return $amount; // fallback: no conversion
}

function coingecko_search(string $q): array {
    $url = 'https://api.coingecko.com/api/v3/search?query=' . urlencode($q);
    $res = http_json($url, 10);
    $out = [];
    if ($res['status'] >= 200 && $res['status'] < 300 && isset($res['data']['coins'])) {
        foreach ($res['data']['coins'] as $coin) {
            $id = (string)($coin['id'] ?? '');
            $name = (string)($coin['name'] ?? '');
            $symbol = (string)($coin['symbol'] ?? '');
            $thumb = (string)($coin['thumb'] ?? '');
            if ($id !== '') { $out[] = ['id' => $id, 'name' => $name, 'symbol' => strtoupper($symbol), 'image' => $thumb]; }
        }
    }
    return ['ok' => !empty($out), 'items' => $out, 'status' => $res['status'], 'error' => $res['error']];
}

function coingecko_price(string $id, string $vs = 'eur'): array {
    $url = 'https://api.coingecko.com/api/v3/simple/price?ids=' . urlencode($id) . '&vs_currencies=' . urlencode($vs);
    $res = http_json($url, 10);
    if ($res['status'] >= 200 && $res['status'] < 300 && is_array($res['data']) && isset($res['data'][$id][$vs])) {
        return ['ok' => true, 'id' => $id, 'price' => (float)$res['data'][$id][$vs], 'currency' => strtoupper($vs)];
    }
    return ['ok' => false, 'id' => $id];
}

function coingecko_coin_info(string $id): array {
    $url = 'https://api.coingecko.com/api/v3/coins/' . urlencode($id) . '?localization=false&tickers=false&market_data=false&community_data=false&developer_data=false&sparkline=false';
    $res = http_json($url, 10);
    if ($res['status'] >= 200 && $res['status'] < 300 && isset($res['data']['id'])) {
        $name = (string)($res['data']['name'] ?? '');
        $image = (string)($res['data']['image']['small'] ?? $res['data']['image']['thumb'] ?? '');
        $symbol = (string)($res['data']['symbol'] ?? '');
        return ['ok' => true, 'id' => $id, 'name' => $name, 'image' => $image, 'symbol' => strtoupper($symbol)];
    }
    return ['ok' => false, 'id' => $id];
}

function cryptocompare_price(string $fsym, string $tsym = 'EUR'): array {
    $fsym = strtoupper(trim($fsym)); $tsym = strtoupper(trim($tsym));
    if ($fsym === '' || $tsym === '') { return ['ok' => false, 'error' => 'invalid_symbol']; }
    $url = 'https://min-api.cryptocompare.com/data/price?fsym=' . urlencode($fsym) . '&tsyms=' . urlencode($tsym);
    $res = http_json_retry($url, 8, 3, 200);
    if ($res['status'] >= 200 && $res['status'] < 300 && isset($res['data'][$tsym])) {
        return ['ok' => true, 'fsym' => $fsym, 'price' => (float)$res['data'][$tsym], 'currency' => $tsym, 'provider' => 'cryptocompare'];
    }
    return ['ok' => false, 'fsym' => $fsym];
}

function binance_pair_price(string $base, string $quote = 'EUR'): array {
    $base = strtoupper(trim($base)); $quote = strtoupper(trim($quote));
    if ($base === '' || $quote === '') { return ['ok' => false, 'error' => 'invalid_pair']; }
    $pair = $base . $quote; // ex: BTCEUR
    $url = 'https://api.binance.com/api/v3/ticker/price?symbol=' . urlencode($pair);
    $res = http_json_retry($url, 8, 3, 200);
    if ($res['status'] >= 200 && $res['status'] < 300 && is_array($res['data']) && isset($res['data']['price'])) {
        return ['ok' => true, 'pair' => $pair, 'price' => (float)$res['data']['price'], 'currency' => $quote, 'provider' => 'binance'];
    }
    foreach (['USDT','USD'] as $altQuote) {
        $pair2 = $base . $altQuote;
        $url2 = 'https://api.binance.com/api/v3/ticker/price?symbol=' . urlencode($pair2);
        $res2 = http_json_retry($url2, 8, 3, 200);
        if ($res2['status'] >= 200 && $res2['status'] < 300 && is_array($res2['data']) && isset($res2['data']['price'])) {
            $p = (float)$res2['data']['price'];
            $eur = ($altQuote === 'USD') ? fx_convert_amount($p, 'USD', 'EUR') : fx_convert_amount($p, 'USD', 'EUR');
            return ['ok' => true, 'pair' => $pair2, 'price' => (float)$eur, 'currency' => 'EUR', 'provider' => 'binance'];
        }
    }
    return ['ok' => false, 'pair' => $pair];
}

function crypto_price_aggregator(string $id, string $symbol = '', string $vs = 'eur'): array {
    $vs = strtolower(trim($vs));
    $cg = coingecko_price($id, $vs);
    if (!empty($cg['ok'])) {
        return ['ok' => true, 'id' => $id, 'price' => (float)$cg['price'], 'currency' => strtoupper($vs), 'provider' => 'coingecko'];
    }
    return ['ok' => false, 'id' => $id];
}

function stooq_quote(string $symbol): array {
    return ['ok' => false, 'symbol' => strtoupper($symbol)];
    $s = strtolower(trim($symbol));
    if ($s === '') { return ['ok' => false]; }
    $url = 'https://stooq.com/q/l/?s=' . urlencode($s) . '&i=d';
    $res = http_json_retry($url, 8, 3, 200);
    $raw = (string)($res['raw'] ?? '');
    $lines = explode("\n", trim($raw));
    if (!empty($lines)) {
        $parts = str_getcsv($lines[0]);
        if (count($parts) >= 2 && is_numeric($parts[1])) {
            return ['ok' => true, 'symbol' => strtoupper($symbol), 'price' => (float)$parts[1], 'currency' => 'USD', 'provider' => 'stooq'];
        }
    }
    return ['ok' => false, 'symbol' => strtoupper($symbol)];
}

function equity_quote_aggregator(string $symbol): array {
    return ['ok' => false, 'symbol' => $symbol];
}

function refresh_manual_assets(PDO $pdo, int $userId, int $ttlSeconds = 300): void {
    $sql = "SELECT c.id_compte, c.categorie, c.external_id, c.updated_at, c.balance_eur, pm.quantity
            FROM compte c
            LEFT JOIN positions_manual pm
              ON pm.user_id = c.user_id AND pm.categorie = c.categorie AND pm.external_id = c.external_id
            WHERE c.user_id = ? AND c.source = 'manual' AND c.status = 'active'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS valuations_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            external_id VARCHAR(128) NOT NULL,
            categorie ENUM('actions_fonds','crypto','immobilier','assurance_vie','autres') NOT NULL,
            value_eur DECIMAL(20,8) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_user_asset_time (user_id, external_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (Throwable $e) { /* ignore */ }

    foreach ($rows as $r) {
        $id = (int)$r['id_compte'];
        $cat = (string)$r['categorie'];
        $ext = (string)$r['external_id'];
        $qty = isset($r['quantity']) ? (float)$r['quantity'] : 1.0;
        $eurVal = null;

        $lastUpdated = isset($r['updated_at']) ? strtotime((string)$r['updated_at']) : 0;
        $nowTs = time();
        $isStale = ($nowTs - $lastUpdated) > max(60, (int)$ttlSeconds);

        if ($isStale) {
            if ($cat === 'crypto' && $ext !== '') {
                $q = crypto_price_aggregator($ext, '', 'eur');
                if (!empty($q['ok'])) { $eurVal = (float)$q['price'] * $qty; }
            }
            if ($eurVal !== null) {
                $upd = $pdo->prepare("UPDATE compte SET balance = ?, balance_eur = ?, updated_at = NOW() WHERE id_compte = ?");
                $upd->execute([$eurVal, $eurVal, $id]);
            } else {
                $eurVal = isset($r['balance_eur']) ? (float)$r['balance_eur'] : null;
            }
        } else {
            $eurVal = isset($r['balance_eur']) ? (float)$r['balance_eur'] : null;
        }

        if ($eurVal !== null) {
            try {
                $cutoff = date('Y-m-d H:i:s', $nowTs - max(60, (int)$ttlSeconds));
                $chk = $pdo->prepare("SELECT id FROM valuations_history WHERE user_id = ? AND external_id = ? AND created_at >= ? ORDER BY created_at DESC LIMIT 1");
                $chk->execute([$userId, $ext, $cutoff]);
                $exists = $chk->fetch(PDO::FETCH_ASSOC);
                if (!$exists) {
                    $ins = $pdo->prepare("INSERT INTO valuations_history (user_id, external_id, categorie, value_eur, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $ins->execute([$userId, $ext, $cat, $eurVal]);
                }
            } catch (Throwable $e) { /* ignore */ }
        }
    }
}

?>
