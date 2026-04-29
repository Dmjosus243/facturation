<?php

require_once '../includes/fonctions-auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['webauthn_username'] ?? '';
    
    if (!$username) {
        echo json_encode(['success' => false, 'message' => 'Nom d\'utilisateur manquant']);
        exit;
    }
    
    $users = chargerUtilisateurs();
    $user = null;
    
    foreach ($users as $u) {
        if ($u['username'] === $username) {
            $user = $u;
            break;
        }
    }
    
    if ($user) {
        connecterUtilisateur($user);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>