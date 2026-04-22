<?php
// modules/produits/lire.php
require_once __DIR__ . '/../../auth/session.php';
require_login();
require_once __DIR__ . '/../../includes/fonctions-produits.php';

$id = $_GET['id'] ?? '';
$barcode = $_GET['barcode'] ?? '';

$product = null;
if ($id) {
    foreach (load_products() as $p) {
        if ($p['id'] === $id) { $product = $p; break; }
    }
} elseif ($barcode) {
    $product = find_product_by_barcode($barcode);
}

if (!$product) {
    echo "Produit introuvable";
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Détails produit</title></head>
<body>
<h1><?=htmlspecialchars($product['name'])?></h1>
<ul>
  <li>Code-barres: <?=htmlspecialchars($product['barcode'])?></li>
  <li>Prix: <?=number_format($product['price'],2,',',' ')?> €</li>
  <li>TVA: <?=number_format($product['vat_rate'],2,',',' ')?>%</li>
  <li>Stock: <?=intval($product['stock'])?></li>
  <li>Description: <?=htmlspecialchars($product['description'])?></li>
</ul>
<a href="liste.php">Retour à la liste</a>
</body>
</html>
