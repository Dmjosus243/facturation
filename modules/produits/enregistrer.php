<?php
require_once '../../includes/fonctions-auth.php';
require_once '../../includes/fonctions-produits.php';

if (!estConnecte()) {
    header('Location: ../../auth/login.php');
    exit;
}

$message = '';
$produit = null;
$isEdit = false;
$code_barre_prefill = '';

// Vérifier si on reçoit un code-barres depuis le scanner (produit non trouvé)
if (isset($_GET['code']) && !empty($_GET['code'])) {
    $code_barre_prefill = $_GET['code'];
}

if (isset($_GET['id'])) {
    $isEdit = true;
    $produit = getProduitById($_GET['id']);
    if (!$produit) {
        $message = 'Produit non trouvé';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prix_ht = floatval($_POST['prix_ht'] ?? 0);
    $code_barre = $_POST['code_barre'] ?? '';
    $tva = floatval($_POST['tva'] ?? TVA_RATE);
    
    if ($nom && $prix_ht > 0) {
        if (isset($_POST['id']) && $_POST['id']) {
            modifierProduit($_POST['id'], $nom, $prix_ht, $code_barre, $tva);
            $message = 'Produit modifié avec succès';
        } else {
            ajouterProduit($nom, $prix_ht, $code_barre, $tva);
            $message = 'Produit ajouté avec succès';
        }
    } else {
        $message = 'Veuillez remplir tous les champs obligatoires';
    }
}

require_once '../../includes/header.php';
?>

<h2><?php echo $isEdit ? 'Modifier le produit' : 'Ajouter un produit'; ?></h2>

<?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="post" class="form-produit">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?php echo $produit['id']; ?>">
    <?php endif; ?>
    
    <label>Nom du produit *</label>
    <input type="text" name="nom" value="<?php echo htmlspecialchars($produit['nom'] ?? ''); ?>" required>
    
    <label>Prix HT *</label>
    <input type="number" step="0.01" name="prix_ht" value="<?php echo $produit['prix_ht'] ?? ''; ?>" required>
    
    <label>
        Code-barres 
        <button type="button" class="btn-scanner-small" onclick="scannerCodeBarre()">📷 Scanner</button>
    </label>
    <input type="text" name="code_barre" id="code_barre" value="<?php echo htmlspecialchars($produit['code_barre'] ?? $code_barre_prefill); ?>">
    
    <label>TVA (0-1)</label>
    <input type="number" step="0.01" name="tva" value="<?php echo $produit['tva'] ?? TVA_RATE; ?>">
    
    <button type="submit">Enregistrer</button>
    <a href="liste.php" class="btn">Annuler</a>
</form>

<script>
// Fonction pour scanner le code-barres directement dans le champ
async function scannerCodeBarre() {
    if ('BarcodeDetector' in window) {
        try {
            const barcodeDetector = new BarcodeDetector({ 
                formats: ['code_128', 'ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_39'] 
            });
            
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            
            const video = document.createElement('video');
            video.srcObject = stream;
            video.setAttribute('playsinline', '');
            await video.play();
            
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
            // Créer modal
            const modal = document.createElement('div');
            modal.className = 'scanner-modal';
            modal.innerHTML = `
                <div class="scanner-modal-content">
                    <div class="scanner-modal-header">
                        <h3>Scannez le code-barres</h3>
                        <button class="close-modal">&times;</button>
                    </div>
                    <div class="scanner-modal-body">
                        <video id="scannerVideo" autoplay playsinline></video>
                        <div class="scan-area"></div>
                    </div>
                    <div class="scanner-modal-footer">
                        <button class="btn-cancel">Annuler</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            const modalVideo = modal.querySelector('#scannerVideo');
            modalVideo.srcObject = stream;
            
            const scanInterval = setInterval(async () => {
                if (!modal.isConnected) {
                    clearInterval(scanInterval);
                    stream.getTracks().forEach(track => track.stop());
                    return;
                }
                
                canvas.width = modalVideo.videoWidth;
                canvas.height = modalVideo.videoHeight;
                if (canvas.width > 0) {
                    context.drawImage(modalVideo, 0, 0, canvas.width, canvas.height);
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    
                    try {
                        const codes = await barcodeDetector.detect(imageData);
                        if (codes.length > 0) {
                            clearInterval(scanInterval);
                            stream.getTracks().forEach(track => track.stop());
                            
                            const codeInput = document.getElementById('code_barre');
                            codeInput.value = codes[0].rawValue;
                            codeInput.style.backgroundColor = '#d4edda';
                            setTimeout(() => {
                                codeInput.style.backgroundColor = '';
                            }, 1000);
                            
                            modal.remove();
                            
                            // Optionnel: rechercher automatiquement le produit
                            verifierProduitExistant(codes[0].rawValue);
                        }
                    } catch (e) {
                        console.error('Erreur détection:', e);
                    }
                }
            }, 100);
            
            modal.querySelector('.close-modal').onclick = () => {
                clearInterval(scanInterval);
                stream.getTracks().forEach(track => track.stop());
                modal.remove();
            };
            
            modal.querySelector('.btn-cancel').onclick = () => {
                clearInterval(scanInterval);
                stream.getTracks().forEach(track => track.stop());
                modal.remove();
            };
            
        } catch (err) {
            console.error('Erreur caméra:', err);
            alert('Impossible d\'accéder à la caméra. Vérifiez les permissions.');
        }
    } else {
        alert('Le scanner par caméra n\'est pas supporté sur ce navigateur.\nUtilisez la saisie manuelle.');
    }
}

// Vérifier si le produit existe déjà via AJAX
function verifierProduitExistant(code) {
    fetch(`recherche-api.php?code=${encodeURIComponent(code)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.produit) {
                if (confirm(`Le produit "${data.produit.nom}" existe déjà. Voulez-vous le modifier ?`)) {
                    window.location.href = `enregistrer.php?id=${data.produit.id}`;
                }
            }
        })
        .catch(err => console.error(err));
}

// Si un code a été pré-rempli via GET, vérifier automatiquement
<?php if (!empty($code_barre_prefill) && !$isEdit): ?>
document.addEventListener('DOMContentLoaded', function() {
    verifierProduitExistant('<?php echo addslashes($code_barre_prefill); ?>');
});
<?php endif; ?>

// Animation sur le champ code-barres
const codeInput = document.getElementById('code_barre');
if (codeInput) {
    codeInput.addEventListener('input', function() {
        if (this.value.length >= 8) {
            this.style.borderColor = '#28a745';
        } else {
            this.style.borderColor = '#ddd';
        }
    });
}
</script>

<style>
.btn-scanner-small {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 20px;
    padding: 4px 12px;
    margin-left: 10px;
    font-size: 12px;
    cursor: pointer;
}

.btn-scanner-small:hover {
    transform: scale(1.05);
}
</style>

<?php require_once '../../includes/footer.php'; ?>