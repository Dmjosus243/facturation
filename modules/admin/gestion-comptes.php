<?php
// admin/gestion-comptes.php
require_once '../includes/fonctions-auth.php';

if (!estAdmin()) {
    header('Location: ../auth/login.php');
    exit;
}

$users = chargerUtilisateurs();
require_once '../includes/header.php';
?>

<h2>Gestion des comptes utilisateurs</h2>
<a href="ajouter-compte.php" class="btn">Ajouter un compte</a>

<table class="table-users">
    <thead>
        <tr><th>ID</th><th>Nom d'utilisateur</th><th>Rôle</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo $user['role']; ?></td>
            <td>
                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                    <a href="supprimer-compte.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Supprimer ce compte ?')">Supprimer</a>
                <?php else: ?>
                    <em>Compte actuel</em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>