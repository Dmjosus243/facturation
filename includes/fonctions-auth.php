<?php

require_once dirname(__DIR__) . '/config/config.php';

function chargerUtilisateurs() {
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    $content = file_get_contents(USERS_FILE);
    return json_decode($content, true) ?: [];
}

function sauvegarderUtilisateurs($users) {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

function verifierIdentifiants($username, $password) {
    $users = chargerUtilisateurs();
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            return $user;
        }
    }
    return false;
}

function connecterUtilisateur($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
}

function estConnecte() {
    return isset($_SESSION['user_id']);
}

function estAdmin() {
    return estConnecte() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function deconnecter() {
    session_destroy();
    $_SESSION = [];
}

function utilisateurCourant() {
    if (!estConnecte()) return null;
    $users = chargerUtilisateurs();
    foreach ($users as $user) {
        if ($user['id'] == $_SESSION['user_id']) {
            return $user;
        }
    }
    return null;
}
?>