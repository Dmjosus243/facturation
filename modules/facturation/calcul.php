<?php
// modules/facturation/calcul.php
require_once '../../includes/fonctions-auth.php';
require_once '../../includes/fonctions-factures.php';

if (!estConnecte()) {
    header('Location: ../../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: nouvelle-facture.php');
    exit;
}

$client_nom = $_POST['client_nom'] ?? '';
$produits_ids = $_POST['produit_id'] ?? [];
$quantites = $_POST['quantite'] ?? [];

$lignes = [];
for ($i = 0; $i < count($produits_ids); $i++) {
    if (!empty($produits_ids[$i]) && !empty($quantites[$i]) && $quantites[$i] > 0) {
        $lignes[] = [
            'produit_id' => intval($produits_ids[$i]),
            'quantite' => floatval($quantites[$i])
        ];
    }
}

if (empty($lignes)) {
    header('Location: nouvelle-facture.php?error=1');
    exit;
}

$facture_id = creerFacture($client_nom, $lignes);
header("Location: afficher-facture.php?id=$facture_id");
exit;
?>