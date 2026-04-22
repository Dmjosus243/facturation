<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/fonctions-auth.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if (verify_credentials($user, $pass)) {
        $_SESSION['user'] = $user;
        header('Location: /facturation/index.php');
        exit;
    } else {
        $error = 'Identifiants invalides';
    }
}
?>
<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Login</title></head><body>
<h1>Connexion</h1>
<?php if($error) echo "<p style='color:red'>".htmlspecialchars($error)."</p>"; ?>
<form method="post">
  <label>Utilisateur <input name="username"></label><br>
  <label>Mot de passe <input name="password" type="password"></label><br>
  <button>Se connecter</button>
</form>
</body></html>
