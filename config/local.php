<?php
// Fichier de configuration locale (non versionné idéalement)
// Définissez ici les constantes si votre serveur PHP ne voit pas les variables d’environnement.

// Powens
if (!defined('POWENS_CLIENT_ID')) {
    define('POWENS_CLIENT_ID', '9941084');
}
if (!defined('POWENS_CLIENT_SECRET')) {
    define('POWENS_CLIENT_SECRET', 'Tio_vDoC3UAtqPMPjTKvwbMgK_cBtJ_F');
}
if (!defined('POWENS_REDIRECT_BASE')) {
    define('POWENS_REDIRECT_BASE', 'http://hugo:8090');
}
if (!defined('POWENS_API_BASE')) {
    define('POWENS_API_BASE', 'https://api.powens.com');
}
if (!defined('POWENS_DOMAIN')) {
    define('POWENS_DOMAIN', 'nova-sandbox.biapi.pro');
}

// Vous pouvez aussi définir APP_DOMAIN si nécessaire, mais en général le serveur HTTP l’infère.

// Pas de sortie