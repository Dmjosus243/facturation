<?php
// modules/produits/enregistrer.php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? '';
if ($action === 'add_to_cart' || $action === 'add_to_cart') {
    $barcode = trim($_POST['barcode'] ?? '');
    $qty = max(1, intval($_POST['qty'] ?? 1));
    if ($barcode === '') { echo json_encode(['ok'=>false,'error'=>'barcode manquant']); exit; }
    $p = find_product_by_barcode($barcode);
    if (!$p) { echo json_encode(['ok'=>false,'error'=>'Produit non trouvé']); exit; }
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $_SESSION['cart'][$barcode] = ($_SESSION['cart'][$barcode] ?? 0) + $qty;
    echo json_encode(['ok'=>true,'cart'=>$_SESSION['cart']]);
    exit;
}

// Enregistrement produit (admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $prod = [
        'id' => $_POST['id'] ?? '',
        'barcode' => $_POST['barcode'] ?? '',
        'name' => $_POST['name'] ?? '',
        'price' => floatval($_POST['price'] ?? 0),
        'vat_rate' => floatval($_POST['vat_rate'] ?? 16),
        'stock' => intval($_POST['stock'] ?? 0),
        'description' => $_POST['description'] ?? ''
    ];
    add_or_update_product($prod);
    header('Location: liste.php');
    exit;
}
