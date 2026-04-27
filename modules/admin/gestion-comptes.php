<?php
require_once __DIR__ . '/../../auth/session.php';
require_login();
require_once __DIR__ . '/../../includes/fonctions-auth.php';

$users=load_users();
?>
<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Comptes</title></head><body>
<h1>Gestion des comptes</h1>
<table border="1">
<tr><th>Utilisateur</th><