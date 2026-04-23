<?php
require_once __DIR__ . '/../../auth/session.php';
require_login();
require_once __DIR__ . '/../../includes/fonctions-produits.php';

$barcode = $_GET['barcode'] ?? '';
$product = $barcode ? find_product_by_barcode($barcode) : null;

// Traitement du formulaire si produit inconnu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$product) {
    $newProd = [
        'id' => uniqid('p_'),
        'barcode' => $barcode,
        'name' => $_POST['name'] ?? '',
        'price' => floatval($_POST['price'] ?? 0),
        'vat_rate' => 16.0, // TVA par défaut
        'stock' => intval($_POST['stock'] ?? 0),
        'expiration' => $_POST['expiration'] ?? '',
        'description' => ''
    ];
    add_or_update_product($newProd);
    header('Location: liste.php');
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Produit</title></head>
<body>
<?php if ($product): ?>
  <h1><?=htmlspecialchars($product['name'])?></h1>
  <ul>
    <li>Code-barres: <?=htmlspecialchars($product['barcode'])?></li>
    <li>Prix HT: <?=number_format($product['price'],2,',',' ')?> CDF</li>
    <li>Stock: <?=intval($product['stock'])?></li>
    <li>Date d’expiration: <?=htmlspecialchars($product['expiration'] ?? 'N/A')?></li>
  </ul>
  <a href="liste.php">Retour à la liste</a>
<?php else: ?>
  <h1>Produit inconnu</h1>
  <form method="post">
    <p>Code-barres: <?=htmlspecialchars($barcode)?></p>
    <label>Nom du produit: <input name="name" required></label><br>
    <label>Prix unitaire HT (CDF): <input type="number" step="0.01" name="price" required></label><br>
    <label>Date d’expiration (MM-JJ-AAAA): <input type="text" name="expiration" required></label><br>
    <label>Quantité initiale en stock: <input type="number" name="stock" required></label><br>
    <button type="submit">Enregistrer</button>
  </form>
<?php endif; ?>
</body>
</html>
