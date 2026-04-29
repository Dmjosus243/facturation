<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Paramètres globaux
define('TVA_RATE', 0.20); // 20%
define('APP_NAME', 'Système de Facturation');
define('BASE_URL', '/facturation/');

// Chemins des fichiers de données
define('DATA_DIR', dirname(__DIR__) . '/data/');
define('PRODUCTS_FILE', DATA_DIR . 'produits.json');
define('INVOICES_FILE', DATA_DIR . 'factures.json');
define('USERS_FILE', DATA_DIR . 'utilisateurs.json');

// Créer le dossier data s'il n'existe pas
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// Initialiser les fichiers JSON s'ils n'existent pas
if (!file_exists(PRODUCTS_FILE)) {
    file_put_contents(PRODUCTS_FILE, json_encode([]));
}
if (!file_exists(INVOICES_FILE)) {
    file_put_contents(INVOICES_FILE, json_encode([]));
}
if (!file_exists(USERS_FILE)) {
    // Créer un admin par défaut (admin/admin123)
    $defaultUsers = [
        [
            'id' => 1,
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin'
        ]
    ];
    file_put_contents(USERS_FILE, json_encode($defaultUsers));
}
?>