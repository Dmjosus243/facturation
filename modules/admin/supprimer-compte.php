<?php
require_once __DIR__ . '/../../auth/session.php';
require_login();
require_once __DIR__ . '/../../includes/fonctions-auth.php';

// Vérifier rôle
if (!check_role('SUPER_ADMIN')) {
    echo "Accès refusé.";
    exit;
}

$username = $_GET['username'] ?? '';
if ($username) {
    $users = load_users();
    $newUsers = [];
    foreach ($users as $u) {
        if ($u['username'] !== $username) {
            $newUsers[] = $u;
        }
    }
    save_users($newUsers);
    header('Location: gestion-comptes.php');
    exit;
} else {
    echo "Utilisateur non spécifié.";
}
