// assets/js/scanner.js - Version avec support caméra et QR code
let scannerActive = false;
let quaggaInitialized = false;
let currentStream = null;

document.addEventListener('DOMContentLoaded', function() {
    // Si nous sommes sur la page de création de facture
    const factureForm = document.getElementById('factureForm');
    if (factureForm) {
        ajouterInterfaceScanner();
    }
    
    // Si nous sommes sur la page de gestion des produits
    const produitForm = document.querySelector('.form-produit');
    if (produitForm) {
        ajouterScannerProduit();
    }
});

function ajouterInterfaceScanner() {
    const form = document.getElementById('factureForm');
    
    // Créer l'interface du scanner
    const scannerDiv = document.createElement('div');
    scannerDiv.className = 'scanner-container';
    scannerDiv.innerHTML = `
        <div class="scanner-header">
            <h3>Scanner de codes-barres</h3>
            <button type="button" id="toggleScannerBtn" class="btn-scanner">📷 Scanner avec caméra</button>
            <input type="text" id="manuelCode" placeholder="Ou saisir code-barres manuellement" class="code-input">
            <button type="button" id="manuelBtn" class="btn-small">Ajouter</button>
        </div>
        <div id="scannerView" style="display:none;">
            <div id="interactive" class="viewport"></div>
            <div id="scannerControls">
                <select id="cameraSelect"></select>
                <button type="button" id="stopScannerBtn" class="btn-small btn-danger">Arrêter caméra</button>
            </div>
        </div>
        <div id="scannerResult" class="scanner-result"></div>
    `;
    
    // Insérer après le client_nom
    const clientField = form.querySelector('.form-group');
    if (clientField) {
        clientField.after(scannerDiv);
    } else {
        form.insertBefore(scannerDiv, form.querySelector('h3'));
    }
    
    // Gestion du scanner manuel
    document.getElementById('manuelBtn').addEventListener('click', function() {
        const code = document.getElementById('manuelCode').value.trim();
        if (code) {
            ajouterProduitParCode(code);
            document.getElementById('manuelCode').value = '';
        }
    });
    
    document.getElementById('manuelCode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const code = this.value.trim();
            if (code) {
                ajouterProduitParCode(code);
                this.value = '';
            }
        }
    });
    
    // Gestion du scanner caméra
    const toggleBtn = document.getElementById('toggleScannerBtn');
    const scannerView = document.getElementById('scannerView');
    
    toggleBtn.addEventListener('click', function() {
        if (scannerActive) {
            arreterScanner();
            scannerView.style.display = 'none';
            toggleBtn.textContent = '📷 Scanner avec caméra';
            scannerActive = false;
        } else {
            scannerView.style.display = 'block';
            demarrerScanner();
            toggleBtn.textContent = '❌ Fermer caméra';
            scannerActive = true;
        }
    });
    
    document.getElementById('stopScannerBtn').addEventListener('click', function() {
        arreterScanner();
        scannerView.style.display = 'none';
        toggleBtn.textContent = '📷 Scanner avec caméra';
        scannerActive = false;
    });
}

function ajouterScannerProduit() {
    const codeBarreInput = document.querySelector('input[name="code_barre"]');
    if (codeBarreInput) {
        const scannerBtn = document.createElement('button');
        scannerBtn.type = 'button';
        scannerBtn.textContent = '📷 Scanner';
        scannerBtn.className = 'btn-scanner-small';
        scannerBtn.style.marginLeft = '10px';
        scannerBtn.onclick = function() {
            scannerCodeBarreProduit(codeBarreInput);
        };
        codeBarreInput.parentNode.appendChild(scannerBtn);
    }
}

async function scannerCodeBarreProduit(inputElement) {
    // Utiliser l'API Barcode Detector si disponible
    if ('BarcodeDetector' in window) {
        try {
            const barcodeDetector = new BarcodeDetector({ formats: ['code_128', 'ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_39', 'qr_code'] });
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            
            const video = document.createElement('video');
            video.srcObject = stream;
            video.setAttribute('playsinline', '');
            await video.play();
            
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
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
                    const imageData = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
                    
                    try {
                        const codes = await barcodeDetector.detect(imageData);
                        if (codes.length > 0) {
                            clearInterval(scanInterval);
                            stream.getTracks().forEach(track => track.stop());
                            inputElement.value = codes[0].rawValue;
                            modal.remove();
                            
                            // Animation de succès
                            inputElement.style.borderColor = 'green';
                            setTimeout(() => {
                                inputElement.style.borderColor = '';
                            }, 2000);
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
            console.error('Erreur BarcodeDetector:', err);
            fallbackScanner(inputElement);
        }
    } else {
        fallbackScanner(inputElement);
    }
}

function fallbackScanner(inputElement) {
    alert('Pour scanner, utilisez l\'appareil photo de votre téléphone.\nCliquez sur "Scanner avec caméra" dans la page facture.');
}

function demarrerScanner() {
    // Utiliser QuaggaJS pour le scanner
    if (typeof Quagga === 'undefined') {
        chargerQuagga();
        return;
    }
    
    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.querySelector('#interactive'),
            constraints: {
                facingMode: "environment",
                width: { ideal: 640 },
                height: { ideal: 480 }
            },
        },
        decoder: {
            readers: [
                "code_128_reader",
                "ean_reader",
                "ean_8_reader",
                "upc_reader",
                "upc_e_reader",
                "code_39_reader",
                "codabar_reader"
            ]
        },
        locate: true,
        locator: {
            patchSize: "medium",
            halfSample: true
        }
    }, function(err) {
        if (err) {
            console.error(err);
            document.getElementById('scannerResult').innerHTML = '<div class="error">Erreur d\'accès à la caméra</div>';
            return;
        }
        Quagga.start();
        quaggaInitialized = true;
    });
    
    Quagga.onDetected(function(result) {
        const code = result.codeResult.code;
        document.getElementById('scannerResult').innerHTML = `<div class="success">Code détecté: ${code}</div>`;
        ajouterProduitParCode(code);
        setTimeout(() => {
            document.getElementById('scannerResult').innerHTML = '';
        }, 2000);
    });
}

function arreterScanner() {
    if (quaggaInitialized && Quagga) {
        Quagga.stop();
        quaggaInitialized = false;
    }
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
        currentStream = null;
    }
}

function chargerQuagga() {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js';
    script.onload = function() {
        demarrerScanner();
    };
    document.head.appendChild(script);
}

function ajouterProduitParCode(code) {
    fetch(`../modules/produits/recherche-api.php?code=${encodeURIComponent(code)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.produit) {
                ajouterLigneProduit(data.produit);
                const resultDiv = document.getElementById('scannerResult');
                if (resultDiv) {
                    resultDiv.innerHTML = `<div class="success">✅ ${data.produit.nom} ajouté !</div>`;
                    setTimeout(() => {
                        resultDiv.innerHTML = '';
                    }, 2000);
                }
            } else {
                // Proposer d'ajouter le produit
                if (confirm(`Produit "${code}" non trouvé. Voulez-vous l'ajouter ?`)) {
                    window.location.href = `../modules/produits/enregistrer.php?code=${encodeURIComponent(code)}`;
                }
            }
        })
        .catch(err => console.error(err));
}

function ajouterLigneProduit(produit) {
    const tableBody = document.querySelector('#lignesTable tbody');
    if (!tableBody) return;
    
    // Vérifier si le produit existe déjà dans la facture
    const existingRows = tableBody.querySelectorAll('tr');
    for (let row of existingRows) {
        const select = row.querySelector('.produit-select');
        if (select && select.value == produit.id) {
            const qteInput = row.querySelector('.quantite');
            if (qteInput) {
                qteInput.value = parseFloat(qteInput.value || 0) + 1;
                return;
            }
        }
    }
    
    // Ajouter nouvelle ligne
    const newRow = tableBody.rows[0].cloneNode(true);
    newRow.querySelectorAll('input, select').forEach(el => el.value = '');
    
    const select = newRow.querySelector('.produit-select');
    for (let opt of select.options) {
        if (opt.value == produit.id) {
            select.value = opt.value;
            break;
        }
    }
    newRow.querySelector('.quantite').value = 1;
    newRow.querySelector('.prix-unitaire').value = produit.prix_ht + ' €';
    tableBody.appendChild(newRow);
}