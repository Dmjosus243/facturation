<?php
// modules/produits/liste.php
require_once '../../includes/fonctions-auth.php';
require_once '../../includes/fonctions-produits.php';

if (!estConnecte()) {
    header('Location: ../../auth/login.php');
    exit;
}

$produits = getProduitsListe();

// Suppression
if (isset($_GET['supprimer']) && estAdmin()) {
    supprimerProduit($_GET['supprimer']);
    header('Location: liste.php');
    exit;
}

require_once '../../includes/header.php';
?>

<h2>Liste des produits</h2>
<a href="enregistrer.php" class="btn">Ajouter un produit</a>

<table class="table-produits">
    <thead>
        <tr><th>ID</th><th>Nom</th><th>Prix HT</th><th>Code-barres</th><th>TVA</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($produits as $p): ?>
        <tr>
            <td><?php echo $p['id']; ?></td>
            <td><?php echo htmlspecialchars($p['nom']); ?></td>
            <td><?php echo number_format($p['prix_ht'], 2); ?> €</td>
            <td><?php echo htmlspecialchars($p['code_barre']); ?></td>
            <td><?php echo ($p['tva'] * 100); ?>%</td>
            <td>
                <a href="lire.php?id=<?php echo $p['id']; ?>">Voir</a>
                <a href="enregistrer.php?id=<?php echo $p['id']; ?>">Modifier</a>
                <?php if (estAdmin()): ?>
                    <a href="?supprimer=<?php echo $p['id']; ?>" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../../includes/footer.php'; ?>