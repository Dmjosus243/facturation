<?php
// rapports/rapport-journalier.php
require_once '../includes/fonctions-auth.php';
require_once '../includes/fonctions-factures.php';

if (!estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}

$date = $_GET['date'] ?? date('Y-m-d');
$stats = getStatsJournalieres($date);

require_once '../includes/header.php';
?>

<h2>Rapport journalier</h2>

<form method="get">
    <label>Date :</label>
    <input type="date" name="date" value="<?php echo $date; ?>">
    <button type="submit">Afficher</button>
</form>

<div class="stats">
    <h3>Résumé du <?php echo date('d/m/Y', strtotime($date)); ?></h3>
    <p>Nombre de factures : <?php echo $stats['nombre']; ?></p>
    <p>Total HT : <?php echo number_format($stats['total_ht'], 2); ?> €</p>
    <p>Total TVA : <?php echo number_format($stats['total_tva'], 2); ?> €</p>
    <p>Total TTC : <?php echo number_format($stats['total_ttc'], 2); ?> €</p>
</div>

<?php if (!empty($stats['factures'])): ?>
    <h3>Liste des factures</h3>
    <table>
        <thead>
            <tr><th>N° facture</th><th>Client</th><th>Heure</th><th>Total TTC</th></tr>
        </thead>
        <tbody>
            <?php foreach ($stats['factures'] as $f): ?>
            <tr>
                <td><?php echo $f['id']; ?></td>
                <td><?php echo htmlspecialchars($f['client_nom']); ?></td>
                <td><?php echo date('H:i', strtotime($f['date'])); ?></td>
                <td><?php echo number_format($f['total_ttc'], 2); ?> €</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>