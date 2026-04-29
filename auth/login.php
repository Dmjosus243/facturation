<?php
// auth/login.php
require_once '../includes/fonctions-auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $user = verifierIdentifiants($username, $password);
    if ($user) {
        connecterUtilisateur($user);
        header('Location: ../index.php');
        exit;
    } else {
        $error = 'Identifiants incorrects';
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="login-form">
    <h2>Connexion</h2>
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Nom d'utilisateur</label>
        <input type="text" name="username" required>
        <label>Mot de passe</label>
        <input type="password" name="password" required>
        <button type="submit">Se connecter</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>