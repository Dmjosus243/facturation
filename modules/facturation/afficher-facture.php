<?php
// modules/facturation/afficher-facture.php
require_once '../../includes/fonctions-auth.php';
require_once '../../includes/fonctions-factures.php';

if (!estConnecte()) {
    header('Location: ../../auth/login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$facture = getFactureById($id);

if (!$facture) {
    header('Location: ../../index.php');
    exit;
}

require_once '../../includes/header.php';
?>

<h2>Facture n° <?php echo $facture['id']; ?></h2>
<p>Date : <?php echo date('d/m/Y H:i', strtotime($facture['date'])); ?></p>
<p>Client : <?php echo htmlspecialchars($facture['client_nom']); ?></p>

<table class="facture-table">
    <thead>
        <tr><th>Produit</th><th>Qté</th><th>Prix unitaire HT</th><th>TVA</th><th>Total HT</th><th>Total TVA</th><th>Total TTC</th></tr>
    </thead>
    <tbody>
        <?php foreach ($facture['lignes'] as $ligne): ?>
        <tr>
            <td><?php echo htmlspecialchars($ligne['produit_nom']); ?></td>
            <td><?php echo $ligne['quantite']; ?></td>
            <td><?php echo number_format($ligne['prix_unitaire_ht'], 2); ?> €</td>
            <td><?php echo ($ligne['tva'] * 100); ?>%</td>
            <td><?php echo number_format($ligne['total_ht'], 2); ?> €</td>
            <td><?php echo number_format($ligne['total_tva'], 2); ?> €</td>
            <td><?php echo number_format($ligne['total_ttc'], 2); ?> €</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="4">Totaux</th><th><?php echo number_format($facture['total_ht'], 2); ?> €</th><th><?php echo number_format($facture['total_tva'], 2); ?> €</th><th><?php echo number_format($facture['total_ttc'], 2); ?> €</th></tr>
    </tfoot>
</table>

<div class="actions">
    <button onclick="window.print()">Imprimer</button>
    <a href="nouvelle-facture.php" class="btn">Nouvelle facture</a>
</div>

<?php require_once '../../includes/footer.php'; ?>