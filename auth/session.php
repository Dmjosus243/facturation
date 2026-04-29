<?php

require_once '../includes/fonctions-auth.php';
if (!estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}
?>