<?php
// admin/supprimer-compte.php
require_once '../includes/fonctions-auth.php';

if (!estAdmin()) {
    header('Location: ../auth/login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$users = chargerUtilisateurs();

// Empêcher la suppression de son propre compte
if ($id == $_SESSION['user_id']) {
    header('Location: gestion-comptes.php?error=Impossible de supprimer votre propre compte');
    exit;
}

$users = array_filter($users, function($u) use ($id) {
    return $u['id'] != $id;
});
sauvegarderUtilisateurs(array_values($users));

header('Location: gestion-comptes.php');
exit;
?>