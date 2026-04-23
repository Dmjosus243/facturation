<?php
// includes/fonctions-produits.php
require_once __DIR__ . '/../config/config.php';

function read_json($path) {
    if (!file_exists($path)) return [];
    $fp = fopen($path, 'r');
    if (!$fp) return [];
    flock($fp, LOCK_SH);
    $data = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    $arr = json_decode($data, true);
    return is_array($arr) ? $arr : [];
}

function write_json_atomic($path, $data) {
    $tmp = $path . '.tmp';
    $fp = fopen($tmp, 'c');
    if (!$fp) return false;
    if (!flock($fp, LOCK_EX)) { fclose($fp); return false; }
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    rename($tmp, $path);
    return true;
}

function load_products() {
    return read_json(PRODUCTS_FILE);
}

function save_products($products) {
    return write_json_atomic(PRODUCTS_FILE, $products);
}

function find_product_by_barcode($barcode) {
    foreach (load_products() as $p) {
        if (isset($p['barcode']) && $p['barcode'] === $barcode) return $p;
    }
    return null;
}

function add_or_update_product($product) {
    $products = load_products();
    // if id provided, update; else create id
    if (empty($product['id'])) $product['id'] = uniqid('p_');
    $found = false;
    foreach ($products as &$p) {
        if ($p['id'] === $product['id'] || (isset($product['barcode']) && $p['barcode'] === $product['barcode'])) {
            $p = array_merge($p, $product);
            $found = true;
            break;
        }
    }
    if (!$found) $products[] = $product;
    return save_products($products);
}
