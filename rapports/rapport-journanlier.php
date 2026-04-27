<?php
require_once __DIR__ . '/../auth/session.php';
require_login();
require_once __DIR__ . '/../includes/fonctions-factures.php';

$today = date('Y-m-d');
$invoices = load_invoices();
$daily = [];

foreach ($invoices as $inv) {
    $date = substr($inv['created_at'], 0, 10); // format YYYY-MM-DD
    if ($date === $today) {
        $daily[] = $inv;
    }
}
?>
<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Rapport journalier</title></head><body>
<h1>Rapport journalier du <?=htmlspecialchars($today)?></h1>
<table border="1">
<tr><th>ID</th><th>Date</th><th>Total HT</th><th>TVA</th><th>Total TTC</th></tr>
<?php foreach($daily as $inv): ?>
<tr>
  <td><?=htmlspecialchars($inv['id'])?></td>
  <td><?=htmlspecialchars($inv['created_at'])?></td>
  <td><?=number_format($inv['total_ht'],2)?></td>
  <td><?=number_format($inv['total_vat'],2)?></td>
  <td><?=number_format($inv['total_ttc'],2)?></td>
</tr>
<?php endforeach; ?>
</table>
<p>Total factures du jour: <?=count($daily)?></p>
</body></html>
