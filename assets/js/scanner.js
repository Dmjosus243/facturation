// assets/js/scanner.js
// Simulation de lecture de code-barres
document.addEventListener('DOMContentLoaded', function() {
    // Si nous sommes sur la page de création de facture
    const inputRecherche = document.createElement('input');
    inputRecherche.type = 'text';
    inputRecherche.placeholder = 'Scanner un code-barres...';
    inputRecherche.id = 'scannerInput';
    inputRecherche.style.margin = '10px 0';
    inputRecherche.style.padding = '8px';
    inputRecherche.style.width = '100%';
    
    const form = document.getElementById('factureForm');
    if (form) {
        // Insérer le champ de recherche après le nom client
        const clientField = form.querySelector('.form-group');
        if (clientField) {
            clientField.after(inputRecherche);
        } else {
            form.insertBefore(inputRecherche, form.querySelector('h3'));
        }
        
        let scanBuffer = '';
        let scanTimeout;
        
        // Fonction pour ajouter un produit trouvé
        function ajouterProduitParCode(code) {
            // Envoyer requête AJAX pour trouver le produit
            fetch(`../../modules/produits/recherche-api.php?code=${encodeURIComponent(code)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.produit) {
                        const tableBody = document.querySelector('#lignesTable tbody');
                        const newRow = tableBody.rows[0].cloneNode(true);
                        newRow.querySelectorAll('input, select').forEach(el => el.value = '');
                        
                        const select = newRow.querySelector('.produit-select');
                        // Chercher l'option correspondant à l'ID
                        for (let opt of select.options) {
                            if (opt.value == data.produit.id) {
                                select.value = opt.value;
                                break;
                            }
                        }
                        newRow.querySelector('.quantite').value = 1;
                        newRow.querySelector('.prix-unitaire').value = data.produit.prix_ht + ' €';
                        tableBody.appendChild(newRow);
                        
                        inputRecherche.value = '';
                    } else {
                        alert('Produit non trouvé: ' + code);
                    }
                })
                .catch(err => console.error(err));
        }
        
        // Capture des codes-barres via clavier (simulation lecteur USB)
        document.addEventListener('keydown', function(e) {
            if (document.activeElement === inputRecherche) return;
            
            if (e.key === 'Enter' && scanBuffer.length > 0) {
                ajouterProduitParCode(scanBuffer);
                scanBuffer = '';
                clearTimeout(scanTimeout);
                e.preventDefault();
            } else if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
                scanBuffer += e.key;
                clearTimeout(scanTimeout);
                scanTimeout = setTimeout(() => {
                    scanBuffer = '';
                }, 100);
            }
        });
        
        // Permettre la recherche manuelle
        inputRecherche.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const code = inputRecherche.value.trim();
                if (code) {
                    ajouterProduitParCode(code);
                }
                e.preventDefault();
            }
        });
    }
});