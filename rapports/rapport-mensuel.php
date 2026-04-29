<?php
// rapports/rapport-mensuel.php
require_once '../includes/fonctions-auth.php';
require_once '../includes/fonctions-factures.php';

if (!estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}

$mois = $_GET['mois'] ?? date('m');
$annee = $_GET['annee'] ?? date('Y');
$stats = getStatsMensuelles($mois, $annee);

require_once '../includes/header.php';
?>

<h2>Rapport mensuel</h2>

<form method="get">
    <label>Mois :</label>
    <select name="mois">
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>" <?php echo ($mois == str_pad($m, 2, '0', STR_PAD_LEFT)) ? 'selected' : ''; ?>>
                <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
            </option>
        <?php endfor; ?>
    </select>
    <label>Année :</label>
    <input type="number" name="annee" value="<?php echo $annee; ?>" min="2020" max="2030">
    <button type="submit">Afficher</button>
</form>

<div class="stats">
    <h3>Résumé de <?php echo date('F Y', strtotime("$annee-$mois-01")); ?></h3>
    <p>Nombre de factures : <?php echo $stats['nombre']; ?></p>
    <p>Total HT : <?php echo number_format($stats['total_ht'], 2); ?> €</p>
    <p>Total TVA : <?php echo number_format($stats['total_tva'], 2); ?> €</p>
    <p>Total TTC : <?php echo number_format($stats['total_ttc'], 2); ?> €</p>
</div>

<?php if (!empty($stats['factures'])): ?>
    <h3>Liste des factures</h3>
    <table>
        <thead>
            <tr><th>N° facture</th><th>Date</th><th>Client</th><th>Total TTC</th></tr>
        </thead>
        <tbody>
            <?php foreach ($stats['factures'] as $f): ?>
            <tr>
                <td><?php echo $f['id']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($f['date'])); ?></td>
                <td><?php echo htmlspecialchars($f['client_nom']); ?></td>
                <td><?php echo number_format($f['total_ttc'], 2); ?> €</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>