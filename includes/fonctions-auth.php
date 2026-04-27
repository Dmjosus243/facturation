<?php
// includes/fonctions-auth.php
require_once __DIR__ . '/../config/config.php';

// ============ Gestion des utilisateurs ============

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
    return password_verify($password, $u['password']);
}

// ============ Fonctions de sécurité ============

function sanitize_input($input) {
    if (is_array($input)) {
        return array_map('sanitize_input', $input);
    }
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

function check_role($role) {
    if (!isset($_SESSION['user']['role'])) {
        return false;
    }
    return $_SESSION['user']['role'] === $role;
}

// ============ Écriture JSON atomique ============

function write_json_atomic($file, $data) {
    $tempFile = $file . '.tmp';
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($tempFile, $json, LOCK_EX) === false) {
        return false;
    }
    
    if (!rename($tempFile, $file)) {
        @unlink($tempFile);
        return false;
    }
    
    return true;
}
