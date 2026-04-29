<?php
// admin/ajouter-compte.php
require_once '../includes/fonctions-auth.php';

if (!estAdmin()) {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    
    if ($username && $password) {
        $users = chargerUtilisateurs();
        // Vérifier si username existe déjà
        $existe = false;
        foreach ($users as $u) {
            if ($u['username'] === $username) {
                $existe = true;
                break;
            }
        }
        if (!$existe) {
            $newId = count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1;
            $users[] = [
                'id' => $newId,
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role
            ];
            sauvegarderUtilisateurs($users);
            $message = 'Compte créé avec succès';
        } else {
            $message = 'Nom d\'utilisateur déjà existant';
        }
    } else {
        $message = 'Veuillez remplir tous les champs';
    }
}

require_once '../includes/header.php';
?>

<h2>Ajouter un compte utilisateur</h2>

<?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="post">
    <label>Nom d'utilisateur</label>
    <input type="text" name="username" required>
    
    <label>Mot de passe</label>
    <input type="password" name="password" required>
    
    <label>Rôle</label>
    <select name="role">
        <option value="user">Utilisateur</option>
        <option value="admin">Administrateur</option>
    </select>
    
    <button type="submit">Créer le compte</button>
    <a href="gestion-comptes.php" class="btn">Annuler</a>
</form>

<?php require_once '../includes/footer.php'; ?>