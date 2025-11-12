Nova — Plateforme d’analyse et de gestion de patrimoine (crypto)

**Aperçu**
- Application web pour analyser et suivre en temps réel un portefeuille d’investissement, actuellement orientée crypto.
- Permet d’ajouter des positions manuelles, de visualiser la performance (24h, 7j), une heatmap marché, des suggestions de picks, et un système d’abonnement Starter/Pro.

**Fonctionnalités clés**
- Suivi du portefeuille et agrégation des positions manuelles.
- Analyse marché: top movers 24h, heatmap des principaux actifs.
- Pages dédiées: `Synthèse`, `Patrimoine`, `Analyse`, `Profil`, `Abonnement`.
- Abonnement Starter/Pro (démo) avec activation locale et intégration Stripe (test).

**Prérequis**
- Windows: WAMP (ou XAMPP) avec Apache, PHP et MySQL.
- macOS: MAMP (ou équivalent avec Apache/PHP/MySQL).
- PHP 8.x recommandé, MySQL 5.7/8.0, extension `PDO` activée.
- Accès à `phpMyAdmin` ou au client MySQL pour importer la base.

**Installation (Windows — WAMP)**
- Installer WAMP et démarrer Apache et MySQL.
- Placer le projet dans le répertoire web de WAMP: `C:\wamp64\www\projet1-ESEO`.
- Créer la base de données `projet_site1`.
  - Via `phpMyAdmin` (`http://localhost/phpmyadmin`):
    - Créer la base `projet_site1`.
    - Importer le fichier SQL: `projet_site1 (3).sql`.
- Configurer les accès DB si nécessaire:
  - Fichier: `config/config.php`
  - Paramètres par défaut: `host=localhost`, `dbname=projet_site1`, `user=root`, `pass=''`.
  - Adapter `user/pass` selon votre installation WAMP.
- Lancer: ouvrir `http://localhost/projet1-ESEO/index.php` ou `http://localhost/projet1-ESEO/html/auth.php`.
- Créer un compte depuis la page Auth, puis accéder aux pages `Synthèse/Patrimoine/Analyse`.

**Installation (macOS — MAMP)**
- Installer MAMP et démarrer Apache et MySQL.
- Placer le projet dans `Applications/MAMP/htdocs/projet1-ESEO`.
- Créer la base `projet_site1` et importer `projet_site1 (3).sql` via `http://localhost:8888/phpMyAdmin` (port par défaut MAMP).
- Adapter les identifiants DB dans `config/config.php` si différents.
- Ouvrir `http://localhost:8888/projet1-ESEO/index.php` ou `http://localhost:8888/projet1-ESEO/html/auth.php`.

**Configuration (optionnelle)**
- Domaine applicatif:
  - L’appli détermine `APP_DOMAIN` automatiquement. Vous pouvez le forcer via la variable d’environnement `APP_DOMAIN` (ex: `http://localhost`).
- Clés Stripe (démo):
  - Variables d’environnement: `STRIPE_SECRET_KEY`, `STRIPE_PUBLISHABLE_KEY`, `STRIPE_WEBHOOK_SECRET`.
  - Des clés de test sont renseignées par défaut dans `config/config.php` pour la démo.
- APIs externes:
  - Moralis: `MORALIS_API_KEY` (optionnel, pour de meilleurs quotas).
  - Powens (sandbox): ajustable via `config/local.php` si besoin (`POWENS_CLIENT_ID`, `POWENS_CLIENT_SECRET`, `POWENS_REDIRECT_BASE`, etc.).
- Surcharges locales:
  - Créez `config/local.php` pour surcharger des constantes sans modifier `config/config.php`.

**Pages utiles**
- `html/auth.php`: inscription/connexion.
- `html/subscription.php`: activation d’abonnement (Starter/Pro) en local.
- `html/app.php`: page Synthèse (accessible si abonnement actif).
- `html/patrimoine.php`: vues et agrégations des actifs manuels.
- `html/analyse.php`: heatmap marché, top movers, etc.

**Simulation d’abonnement en local**
- Sur `html/subscription.php`, un bouton de “Checkout Stripe” est disponible pour la démo (test).
- Pour forcer une activation locale, vous pouvez utiliser le mode débogage: `html/subscription.php?debug=1`, puis cliquer le bouton de simulation (active la DB pour l’utilisateur connecté).

**Structure du projet**
- `html/`: pages PHP (routage et UI).
- `js/pages/`: scripts frontend par page.
- `css/`: styles.
- `api/`: helpers d’agrégation/market.
- `webhooks/stripe.php`: point d’entrée Stripe (démo).
- `config/`: configuration applicative et base de données.

**Notes**
- Ce projet est une démo pédagogique; les clés/API présentes sont prévues pour le test uniquement.
- Évitez d’utiliser `python -m http.server` pour servir le PHP; utilisez WAMP/MAMP/XAMPP.
- Pour un alias réseau (ex: `http://hugo:8090`), créez une entrée dans le fichier `hosts` et configurez le vhost/port en conséquence.

Bon usage et bonne démo !

Objectif
- Petite démo fonctionnelle, sans complexité.

Prérequis (Mac)
- PHP 8+ et MySQL (MariaDB ok)
- Navigateur (Safari/Chrome)

Installation
- Importer `projet_site1 (3).sql` dans MySQL (base: `projet_site1`).
- Dans le terminal, à la racine du projet: `php -S localhost:8000 -t .`
- Ouvrir `http://localhost:8000/index.php`.

Parcours utilisateur
- Accueil → Connexion → Inscription (e‑mail, nom, mot de passe).
- Page Abonnement → choisir Starter ou Pro.
- Cliquer « Checkout Stripe » (Pro).
- Carte test: `4242 4242 4242 4242`, date future, CVC `123`.
- Redirection vers l’app → Abonnement activé 30 jours (démo).

Dépannage
- « Unexpected token / URL n’est pas un JSON »: utilisez bien un serveur PHP (pas un serveur statique). Les endpoints renvoient du JSON.
- Clés Stripe (optionnel): `export STRIPE_SECRET_KEY='sk_test_...'` et `export STRIPE_PUBLISHABLE_KEY='pk_test_...'`.

Remarques
- Fonctionne de base sur Mac avec le serveur PHP intégré.
- Projet en mode test uniquement. N’utilisez jamais des clés live.