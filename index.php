<?php
require_once __DIR__ . '/auth/session.php';
require_login();
?>
<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>POS</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'includes/header.php'; ?>
<h1>Tableau de bord</h1>
<ul>
  <li><a href="modules/produits/liste.php">Produits</a></li>
  <li><a href="modules/facturation/nouvelle-facture.php">Nouvelle facture</a></li>
  <li><a href="admin/gestion-comptes.php">Administration</a></li>
  <li><a href="auth/logout.php">Se déconnecter</a></li>
</ul>
<?php include 'includes/footer.php'; ?>
</body></html>
