<?php

global $pdo;
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

// Assure la présence des colonnes d’abonnement dans la table `users`
function columnExists(PDO $pdo, string $table, string $column): bool {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
        $stmt->execute([$column]);
        return (bool)$stmt->fetch();
    } catch (PDOException $e) {
        // Si SHOW COLUMNS échoue, on considère que la colonne n’existe pas
        return false;
    }
}

function ensureSubscriptionColumns(PDO $pdo): void {
    try {
        if (!columnExists($pdo, 'users', 'subscription_plan')) {
            $pdo->exec("ALTER TABLE `users` ADD COLUMN `subscription_plan` VARCHAR(50) DEFAULT 'none'");
        }
        if (!columnExists($pdo, 'users', 'subscription_start')) {
            $pdo->exec("ALTER TABLE `users` ADD COLUMN `subscription_start` DATE DEFAULT NULL");
        }
        if (!columnExists($pdo, 'users', 'subscription_end')) {
            $pdo->exec("ALTER TABLE `users` ADD COLUMN `subscription_end` DATE DEFAULT NULL");
        }
    } catch (PDOException $e) {
        // En cas d’échec de migration, on ne bloque pas mais on remontera l’erreur à l’update
        error_log('Migration abonnements échouée: ' . $e->getMessage());
    }
}

ensureSubscriptionColumns($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan = $_POST['plan'] ?? 'none';
    if (!in_array($plan, ['starter', 'pro'])) {
        die("Plan invalide.");
    }

    // Simulation de paiement réussi
    $start = date('Y-m-d');
    $end = date('Y-m-d', strtotime('+1 month'));

    $stmt = $pdo->prepare("UPDATE users SET subscription_plan = ?, subscription_start = ?, subscription_end = ? WHERE id = ?");
    $stmt->execute([$plan, $start, $end, $_SESSION['user_id']]);

    $_SESSION['message'] = "Félicitations, votre abonnement $plan est actif jusqu’au $end !";
    header("Location: app.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement - Nova</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen">
    <div class="relative isolate bg-gray-900 px-6 py-24 sm:py-32 lg:px-8">
        <!-- Background decoration -->
        <div aria-hidden="true" class="absolute inset-x-0 -top-3 -z-10 transform-gpu overflow-hidden px-36 blur-3xl">
            <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="mx-auto aspect-[1155/678] w-[72.1875rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20"></div>
        </div>
        
        <!-- Header -->
        <div class="mx-auto max-w-4xl text-center">
            <h2 class="text-base/7 font-semibold text-indigo-400">Abonnement</h2>
            <p class="mt-2 text-5xl font-semibold tracking-tight text-balance text-white sm:text-6xl">Choisissez votre plan</p>
        </div>
        <p class="mx-auto mt-6 max-w-2xl text-center text-lg font-medium text-pretty text-gray-400 sm:text-xl/8">Essai gratuit de 30 jours sur tous les plans. Choisissez le plan qui correspond le mieux à vos besoins.</p>
        
        <!-- Pricing cards -->
        <div class="mx-auto mt-16 grid max-w-lg grid-cols-1 items-center gap-y-6 sm:mt-20 sm:gap-y-0 lg:max-w-4xl lg:grid-cols-2">
            <!-- Starter Plan -->
            <div class="rounded-3xl rounded-t-3xl bg-white/5 p-8 ring-1 ring-white/10 sm:mx-8 sm:rounded-b-none sm:p-10 lg:mx-0 lg:rounded-tr-none lg:rounded-bl-3xl">
                <h3 id="tier-starter" class="text-base/7 font-semibold text-indigo-400">Starter</h3>
                <p class="mt-4 flex items-baseline gap-x-2">
                    <span class="text-5xl font-semibold tracking-tight text-white">27€</span>
                    <span class="text-base text-gray-400">/mois</span>
                </p>
                <p class="mt-6 text-base/7 text-gray-300">Le plan parfait pour commencer avec Nova et découvrir nos fonctionnalités.</p>
                <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-300 sm:mt-10">
                    <li class="flex gap-x-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="h-6 w-5 flex-none text-indigo-400">
                            <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        Analyses de base
                    </li>
                    <li class="flex gap-x-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="h-6 w-5 flex-none text-indigo-400">
                            <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        Jusqu'à 5 projets
                    </li>
                    <li class="flex gap-x-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="h-6 w-5 flex-none text-indigo-400">
                            <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        Support par email
                    </li>
                    <li class="flex gap-x-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="h-6 w-5 flex-none text-indigo-400">
                            <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        Essai gratuit 30 jours
                    </li>
                </ul>
                <div class="mt-8 space-y-3 sm:mt-10">
                    <?php if (isset($_GET['debug']) && $_GET['debug'] === '1'): ?>
                    <form method="POST" class="w-full">
                        <button name="plan" value="starter" type="submit" class="w-full rounded-md bg-white/10 px-3.5 py-2.5 text-center text-sm font-semibold text-white ring-1 ring-inset ring-white/5 hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white/75">
                            Activer (simulation locale)
                        </button>
                    </form>
                    <?php endif; ?>
                    <button type="button" id="checkout-starter" class="w-full rounded-md bg-indigo-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Checkout Stripe
                    </button>
                </div>
            </div>
            
            <!-- Pro Plan -->
            <div class="relative rounded-3xl bg-gray-800 p-8 ring-1 ring-white/10 sm:p-10">
                <h3 id="tier-pro" class="text-base/7 font-semibold text-indigo-400">Pro</h3>
                <p class="mt-4 flex items-baseline gap-x-2">
                    <span class="text-5xl font-semibold tracking-tight text-white">49€</span>
                    <span class="text-base text-gray-400">/mois</span>
                </p>
                <p class="mt-6 text-base/7 text-gray-300">Support dédié et infrastructure avancée pour votre entreprise.</p>
                <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-300 sm:mt-10">
                    <li class="flex gap-x-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="h-6 w-5 flex-none text-indigo-400">
                            <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        Projets illimités
                    </li>
                    <li class="flex gap-x-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="h-6 w-5 flex-none text-indigo-400">
                            <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        Analyses avancées
                    </li>
                    <li class="flex gap-x-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="h-6 w-5 flex-none text-indigo-400">
                            <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        Support prioritaire
                    </li>
                    <li class="flex gap-x-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="h-6 w-5 flex-none text-indigo-400">
                            <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        Automatisations marketing
                    </li>
                    <li class="flex gap-x-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="h-6 w-5 flex-none text-indigo-400">
                            <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        Intégrations personnalisées
                    </li>
                    <li class="flex gap-x-3">
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="h-6 w-5 flex-none text-indigo-400">
                            <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        Essai gratuit 30 jours
                    </li>
                </ul>
                <div class="mt-8 space-y-3 sm:mt-10">
                    <?php if (isset($_GET['debug']) && $_GET['debug'] === '1'): ?>
                    <form method="POST" class="w-full">
                        <button name="plan" value="pro" type="submit" class="w-full rounded-md bg-white/10 px-3.5 py-2.5 text-center text-sm font-semibold text-white ring-1 ring-inset ring-white/5 hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white/75">
                            Activer (simulation locale)
                        </button>
                    </form>
                    <?php endif; ?>
                    <button type="button" id="checkout-pro" class="w-full rounded-md bg-indigo-500 px-3.5 py-2.5 text-center text-sm font-semibold text-white hover:bg-indigo-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                        Checkout Stripe
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function startCheckout(plan) {
            try {
                const res = await fetch('/html/create_subscription_session.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ plan }),
                });
                const data = await res.json();
                if (!res.ok || !data.url) {
                    alert('Erreur Stripe: ' + (data.error || 'Réponse invalide'));
                    return;
                }
                window.location.href = data.url;
            } catch (e) {
                alert('Erreur réseau: ' + e.message);
            }
        }
        document.getElementById('checkout-starter')?.addEventListener('click', () => startCheckout('starter'));
        document.getElementById('checkout-pro')?.addEventListener('click', () => startCheckout('pro'));
    </script>
</body>
</html>
