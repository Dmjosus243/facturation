<?php
require_once __DIR__ . '/../../auth/session.php';
require_login();
require_once __DIR__ . '/../../includes/fonctions-auth.php';

// Vérifier rôle (seuls ADMIN/MANAGER peuvent ajouter)
if (!check_role('SUPER_ADMIN') && !check_role('MANAGER')) {
    http_response_code(403);
    echo "Accès refusé.";
    exit;
}

// Initialisation des variables
$error = null;
$username = '';
$role = 'CAISSIER';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = sanitize_input($_POST['role'] ?? 'CAISSIER');

    // Vérification des champs obligatoires
    if (empty($username) || empty($password)) {
        $error = "Champs obligatoires manquants.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        // Valider le rôle
        $roles_valides = ['CAISSIER', 'MANAGER', 'SUPER_ADMIN'];
        if (!in_array($role, $roles_valides)) {
            $error = "Rôle invalide.";
        } else {
            // Vérifier si l'utilisateur existe déjà
            $users = load_users();
            $user_existe = false;
            foreach ($users as $u) {
                if ($u['username'] === $username) {
                    $user_existe = true;
                    break;
                }
            }
            
            if ($user_existe) {
                $error = "Utilisateur déjà existant.";
            } else {
                // Créer le nouvel utilisateur
                $users[] = [
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role'     => $role
                ];
                save_users($users);
                header('Location: gestion-comptes.php?success=1');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter compte</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<h1>Ajouter un compte utilisateur</h1>
<?php if (!empty($error)) echo "<p style='color:red;font-weight:bold;'>⚠️ ".htmlspecialchars($error)."</p>"; ?>

<form method="post" action="">
  <div>
    <label for="username">Nom d'utilisateur:</label>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
  </div>
  <div>
    <label for="password">Mot de passe:</label>
    <input type="password" id="password" name="password" minlength="6" required>
  </div>
  <div>
    <label for="role">Rôle:</label>
    <select id="role" name="role">
      <option value="CAISSIER" <?php echo ($role === 'CAISSIER') ? 'selected' : ''; ?>>Caissier</option>
      <option value="MANAGER" <?php echo ($role === 'MANAGER') ? 'selected' : ''; ?>>Manager</option>
      <option value="SUPER_ADMIN" <?php echo ($role === 'SUPER_ADMIN') ? 'selected' : ''; ?>>Super Admin</option>
    </select>
  </div>
  <div>
    <button type="submit">Créer</button>
    <a href="gestion-comptes.php">Annuler</a>
  </div>
</form>
<p><a href="gestion-comptes.php">← Retour</a></p>
</body>
</html>
