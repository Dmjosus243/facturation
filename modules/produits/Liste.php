<?php
require_once __DIR__ . '/../../auth/session.php';
require_login();
require_once __DIR__ . '/../../includes/fonctions-produits.php';

$products = load_products();
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Liste des produits</title></head>
<body>
<h1>Produits</h1>
<table border="1" cellpadding="6">
  <thead>
    <tr><th>Nom</th><th>Code-barres</th><th>Prix HT</th><th>Stock</th><th>Action</th></tr>
  </thead>
  <tbody>
    <?php foreach($products as $p): ?>
    <tr>
      <td><?=htmlspecialchars($p['name'])?></td>
      <td><?=htmlspecialchars($p['barcode'])?></td>
      <td><?=number_format($p['price'],2,',',' ')?> CDF</td>
      <td><?=intval($p['stock'])?></td>
      <td><a href="lire.php?barcode=<?=urlencode($p['barcode'])?>">Voir</a></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h2>Rechercher un produit par code-barres</h2>
<form method="get" action="lire.php">
  <input name="barcode" placeholder="Saisir code-barres">
  <button type="submit">Chercher</button>
</form>
</body>
</html>
