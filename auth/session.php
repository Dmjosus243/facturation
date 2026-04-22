<?php
// auth/session.php
session_start();
function require_login() {
    if (empty($_SESSION['user'])) {
        header('Location: /facturation/auth/login.php');
        exit;
    }
}
