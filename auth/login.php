<?php
// auth/login.php - Version avec support empreinte digitale
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

<div class="login-container">
    <div class="login-form">
        <h2>Connexion</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Connexion classique -->
        <form method="post" id="loginForm">
            <label>Nom d'utilisateur</label>
            <input type="text" name="username" id="username" required>
            
            <label>Mot de passe</label>
            <input type="password" name="password" id="password" required>
            
            <button type="submit">Se connecter</button>
        </form>
        
        <!-- Section empreinte digitale / FaceID -->
        <div id="biometricSection" style="display:none; margin-top: 20px;">
            <hr>
            <h3>Connexion biométrique</h3>
            <div id="biometricStatus"></div>
            
            <button type="button" id="useBiometricBtn" class="btn-biometric" onclick="loginWithBiometric()" style="display:none;">
                🔐 Utiliser empreinte digitale / FaceID
            </button>
            
            <button type="button" id="registerBiometricBtn" class="btn-biometric-secondary" onclick="registerBiometric()">
                📱 Enregistrer mon empreinte digitale
            </button>
            
            <input type="hidden" id="biometricUsername">
        </div>
    </div>
</div>

<script src="../assets/js/webauthn.js"></script>

<script>
// Stocker le username pour la biométrie
document.getElementById('username').addEventListener('change', function() {
    document.getElementById('biometricUsername').value = this.value;
});

// Vérifier si l'utilisateur a déjà une empreinte enregistrée
function checkExistingBiometric() {
    const saved = localStorage.getItem('webauthn_credentials');
    if (saved) {
        const creds = JSON.parse(saved);
        const currentUser = document.getElementById('username').value;
        if (currentUser && creds.some(c => c.username === currentUser)) {
            document.getElementById('registerBiometricBtn').style.display = 'none';
            document.getElementById('useBiometricBtn').style.display = 'block';
        } else {
            document.getElementById('registerBiometricBtn').style.display = 'block';
            document.getElementById('useBiometricBtn').style.display = 'none';
        }
    }
}

document.getElementById('username').addEventListener('input', checkExistingBiometric);
</script>

<style>
.btn-biometric {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

.btn-biometric-secondary {
    background: #6c757d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
}

.biometric-icon {
    font-size: 24px;
    margin-right: 10px;
}
</style>

<?php require_once '../includes/footer.php'; ?>