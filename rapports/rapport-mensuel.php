<?php
require_once __DIR__ . '/../auth/session.php';
require_login();
require_once __DIR__ . '/../includes/fonctions-factures.php';

$currentMonth = date('Y-m');
$invoices = load_invoices();
$monthly = [];

foreach ($invoices as $inv) {
    $date = substr($inv['created_at'], 0, 7); // format YYYY-MM
    if ($date === $currentMonth) {
        $monthly[] = $inv;
    }
}
?>
<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Rapport mensuel</title></head><body>
<h1>Rapport mensuel de <?=htmlspecialchars($currentMonth)?></h1>
<table border="1">
<tr><th>ID</th><th>Date</th><th>Total HT</th><th>TVA</th><th>Total TTC</th></tr>
<?php foreach($monthly as $inv): ?>
<tr>
  <td><?=htmlspecialchars($inv['id'])?></td>
  <td><?=htmlspecialchars($inv['created_at'])?></td>
  <td><?=number_format($inv['total_ht'],2)?></td>
  <td><?=number_format($inv['total_vat'],2)?></td>
  <td><?=number_format($inv['total_ttc'],2)?></td>
</tr>
<?php endforeach; ?>
</table>
<p>Total factures du mois: <?=count($monthly)?></p>
</body></html>
