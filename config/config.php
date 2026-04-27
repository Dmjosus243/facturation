<?php
// config/config.php
define('DATA_DIR', __DIR__ . '/../data');
define('INVOICES_FILE', DATA_DIR . '/factures.json');
define('PRODUCTS_FILE', DATA_DIR . '/produits.json');
define('USERS_FILE', DATA_DIR . '/utilisateurs.json');
define('TVA', 0.18);

if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
if (!file_exists(PRODUCTS_FILE)) file_put_contents(PRODUCTS_FILE, json_encode([], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
if (!file_exists(INVOICES_FILE)) file_put_contents(INVOICES_FILE, json_encode([], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
if (!file_exists(USERS_FILE)) file_put_contents(USERS_FILE, json_encode([], JSON_UNESCAPED_UNICODE));
?>