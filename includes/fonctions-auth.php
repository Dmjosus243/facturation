<?php
// includes/fonctions-auth.php
require_once __DIR__ . '/../config/config.php';

function load_users() {
    $path = USERS_FILE;
    if (!file_exists($path)) return [];
    $data = file_get_contents($path);
    $arr = json_decode($data, true);
    return is_array($arr) ? $arr : [];
}

function save_users($users) {
    return write_json_atomic(USERS_FILE, $users);
}

function find_user($username) {
    foreach (load_users() as $u) {
        if ($u['username'] === $username) return $u;
    }
    return null;
}

function verify_credentials($username, $password) {
    $u = find_user($username);
    if (!$u) return false;
    // stockez les mots de passe hachés en production; ici on supporte password_hash
    return password_verify($password, $u['password']);
}
