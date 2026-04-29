<?php
// modules/produits/lire.php
require_once '../../includes/fonctions-auth.php';
require_once '../../includes/fonctions-produits.php';

if (!estConnecte()) {
    header('Location: ../../auth/login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$produit = getProduitById($id);

require_once '../../includes/header.php';
?>

<h2>Détail du produit</h2>

<?php if ($produit): ?>
    <table class="detail-produit">
        <tr><th>ID</th><td><?php echo $produit['id']; ?></td></tr>
        <tr><th>Nom</th><td><?php echo htmlspecialchars($produit['nom']); ?></td></tr>
        <tr><th>Prix HT</th><td><?php echo number_format($produit['prix_ht'], 2); ?> €</td></tr>
        <tr><th>Code-barres</th><td><?php echo htmlspecialchars($produit['code_barre']); ?></td></tr>
        <tr><th>TVA</th><td><?php echo ($produit['tva'] * 100); ?>%</td></tr>
        <tr><th>Prix TTC</th><td><?php echo number_format($produit['prix_ht'] * (1 + $produit['tva']), 2); ?> €</td></tr>
    </table>
    <a href="liste.php" class="btn">Retour</a>
<?php else: ?>
    <p>Produit non trouvé</p>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>