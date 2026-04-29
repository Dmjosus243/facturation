<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <header>
        <h1><?php echo APP_NAME; ?></h1>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="<?php echo BASE_URL; ?>modules/facturation/nouvelle-facture.php">Nouvelle facture</a>
                <a href="<?php echo BASE_URL; ?>modules/produits/liste.php">Produits</a>
                <a href="<?php echo BASE_URL; ?>rapports/rapport-journalier.php">Rapport journalier</a>
                <a href="<?php echo BASE_URL; ?>rapports/rapport-mensuel.php">Rapport mensuel</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="<?php echo BASE_URL; ?>admin/gestion-comptes.php">Gestion comptes</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>auth/logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>auth/login.php">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>