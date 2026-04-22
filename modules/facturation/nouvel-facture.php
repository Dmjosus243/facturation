<?php
require_once __DIR__ . '/../../auth/session.php';
require_login();
require_once __DIR__ . '/../../includes/fonctions-produits.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_invoice'])) {
    // construire items depuis le panier
    $items = [];
    foreach ($_SESSION['cart'] as $barcode => $qty) {
        $p = find_product_by_barcode($barcode);
        if (!$p) continue;
        $items[] = [
            'product_id'=>$p['id'],
            'barcode'=>$p['barcode'],
            'name'=>$p['name'],
            'qty'=>$qty,
            'unit_price'=>$p['price'],
            'vat_rate'=>$p['vat_rate']
        ];
    }
    $invoice_id = create_invoice($items, ['cashier'=>$_SESSION['user'] ?? 'unknown']);
    if ($invoice_id) {
        $_SESSION['cart'] = [];
        header('Location: afficher-facture.php?id=' . urlencode($invoice_id));
        exit;
    } else {
        $error = 'Impossible de créer la facture';
    }
}
?>
<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Nouvelle facture</title></head><body>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<h1>Nouvelle facture</h1>

<!-- Zone scanner + saisie manuelle -->
<div id="scanner">
  <video id="video" width="320" height="240"></video>
  <p>Code détecté: <span id="detected">—</span></p>
  <input id="barcodeInput" placeholder="Code-barres">
  <input id="qtyInput" type="number" value="1" min="1">
  <button id="addManual">Ajouter</button>
</div>

<!-- Panier -->
<form method="post">
  <table border="1">
    <thead><tr><th>Produit</th><th>Qté</th><th>PU</th><th>HT</th></tr></thead>
    <tbody>
    <?php
    $total_ht = 0; $total_vat = 0;
    foreach ($_SESSION['cart'] as $barcode => $qty) {
        $p = find_product_by_barcode($barcode);
        if (!$p) continue;
        $line_ht = $p['price'] * $qty;
        $line_vat = $line_ht * ($p['vat_rate']/100.0);
        $total_ht += $line_ht; $total_vat += $line_vat;
        echo "<tr><td>".htmlspecialchars($p['name'])."</td><td>{$qty}</td><td>".number_format($p['price'],2)."</td><td>".number_format($line_ht,2)."</td></tr>";
    }
    ?>
    </tbody>
  </table>
  <p>Total HT: <?=number_format($total_ht,2)?> TVA: <?=number_format($total_vat,2)?> Total TTC: <?=number_format($total_ht+$total_vat,2)?></p>
  <button name="create_invoice" type="submit">Créer facture</button>
</form>

<script src="/facturation/assets/js/scanner.js"></script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body></html>
