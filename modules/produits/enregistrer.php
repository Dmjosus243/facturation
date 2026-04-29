<?php
// modules/produits/enregistrer.php
require_once '../../includes/fonctions-auth.php';
require_once '../../includes/fonctions-produits.php';

if (!estConnecte()) {
    header('Location: ../../auth/login.php');
    exit;
}

$message = '';
$produit = null;
$isEdit = false;

if (isset($_GET['id'])) {
    $isEdit = true;
    $produit = getProduitById($_GET['id']);
    if (!$produit) {
        $message = 'Produit non trouvé';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prix_ht = floatval($_POST['prix_ht'] ?? 0);
    $code_barre = $_POST['code_barre'] ?? '';
    $tva = floatval($_POST['tva'] ?? TVA_RATE);
    
    if ($nom && $prix_ht > 0) {
        if (isset($_POST['id']) && $_POST['id']) {
            modifierProduit($_POST['id'], $nom, $prix_ht, $code_barre, $tva);
            $message = 'Produit modifié avec succès';
        } else {
            ajouterProduit($nom, $prix_ht, $code_barre, $tva);
            $message = 'Produit ajouté avec succès';
        }
    } else {
        $message = 'Veuillez remplir tous les champs obligatoires';
    }
}

require_once '../../includes/header.php';
?>

<h2><?php echo $isEdit ? 'Modifier le produit' : 'Ajouter un produit'; ?></h2>

<?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="post" class="form-produit">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?php echo $produit['id']; ?>">
    <?php endif; ?>
    
    <label>Nom du produit *</label>
    <input type="text" name="nom" value="<?php echo htmlspecialchars($produit['nom'] ?? ''); ?>" required>
    
    <label>Prix HT *</label>
    <input type="number" step="0.01" name="prix_ht" value="<?php echo $produit['prix_ht'] ?? ''; ?>" required>
    
    <label>Code-barres</label>
    <input type="text" name="code_barre" value="<?php echo htmlspecialchars($produit['code_barre'] ?? ''); ?>">
    
    <label>TVA (0-1)</label>
    <input type="number" step="0.01" name="tva" value="<?php echo $produit['tva'] ?? TVA_RATE; ?>">
    
    <button type="submit">Enregistrer</button>
    <a href="liste.php" class="btn">Annuler</a>
</form>

<?php require_once '../../includes/footer.php'; ?>