<?php
require_once __DIR__ . '/../../auth/session.php';
require_login();
require_once __DIR__ . '/../../includes/fonctions-factures.php';
$id = $_GET['id'] ?? '';
$inv = load_invoice($id);
if (!$inv) { echo "Facture introuvable"; exit; }
?>
<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Facture</title></head><body>
<h1>Facture <?=htmlspecialchars($inv['id'])?></h1>
<p>Date: <?=htmlspecialchars($inv['created_at'])?></p>
<table border="1">
<thead><tr><th>Produit</th><th>Qté</th><th>PU</th><th>HT</th><th>TVA</th></tr></thead>
<tbody>
<?php foreach($inv['items'] as $it): ?>
<tr>
  <td><?=htmlspecialchars($it['name'])?></td>
  <td><?=intval($it['qty'])?></td>
  <td><?=number_format($it['unit_price'],2,',',' ')?></td>
  <td><?=number_format($it['line_ht'],2,',',' ')?></td>
  <td><?=number_format($it['line_vat'],2,',',' ')?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<p>Total HT: <?=number_format($inv['total_ht'],2,',',' ')?> TVA: <?=number_format($inv['total_vat'],2,',',' ')?> TTC: <?=number_format($inv['total_ttc'],2,',',' ')?></p>
<button onclick="window.print()">Imprimer</button>
</body></html>
