<?php
// modules/produits/liste.php
require_once __DIR__ . '/../../auth/session.php';
require_login();
require_once __DIR__ . '/../../includes/fonctions-produits.php';

$products = load_products();
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Liste des produits</title></head>
<body>
<h1>Produits disponibles</h1>
<table border="1" cellpadding="6">
  <thead>
    <tr><th>Nom</th><th>Code-barres</th><th>Prix</th><th>Stock</th><th>Action</th></tr>
  </thead>
  <tbody>
    <?php foreach($products as $p): ?>
    <tr>
      <td><?=htmlspecialchars($p['name'])?></td>
      <td><?=htmlspecialchars($p['barcode'])?></td>
      <td><?=number_format($p['price'],2,',',' ')?> €</td>
      <td><?=intval($p['stock'])?></td>
      <td>
        <a href="lire.php?id=<?=urlencode($p['id'])?>">Voir</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p><a href="enregistrer.php">Ajouter un produit</a></p>
</body>
</html>
