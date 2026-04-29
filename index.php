<?php
require_once 'includes/fonctions-auth.php';
require_once 'includes/header.php';
?>

<div class="dashboard">
    <h2>Bienvenue sur le système de facturation</h2>
    <?php if (estConnecte()): ?>
        <p>Connecté en tant que : <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <div class="menu-rapide">
            <a href="modules/facturation/nouvelle-facture.php" class="btn">Créer une facture</a>
            <a href="modules/produits/liste.php" class="btn">Gérer les produits</a>
            <a href="rapports/rapport-journalier.php" class="btn">Voir les rapports</a>
        </div>
    <?php else: ?>
        <p>Veuillez vous <a href="auth/login.php">connecter</a> pour accéder à l'application.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>