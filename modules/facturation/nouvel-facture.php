<?php
// modules/facturation/nouvelle-facture.php
require_once '../../includes/fonctions-auth.php';
require_once '../../includes/fonctions-produits.php';

if (!estConnecte()) {
    header('Location: ../../auth/login.php');
    exit;
}

$produits = getProduitsListe();

require_once '../../includes/header.php';
?>

<h2>Nouvelle facture</h2>

<form method="post" action="calcul.php" id="factureForm">
    <div class="form-group">
        <label>Nom du client</label>
        <input type="text" name="client_nom" required>
    </div>
    
    <h3>Lignes de facture</h3>
    <table id="lignesTable">
        <thead>
            <tr><th>Produit</th><th>Quantité</th><th>Prix unitaire HT</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <tr class="ligne">
                <td>
                    <select name="produit_id[]" class="produit-select" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($produits as $p): ?>
                            <option value="<?php echo $p['id']; ?>" data-prix="<?php echo $p['prix_ht']; ?>">
                                <?php echo htmlspecialchars($p['nom']); ?> (<?php echo $p['prix_ht']; ?>€)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" name="quantite[]" class="quantite" step="0.01" min="0.01" required></td>
                <td><input type="text" class="prix-unitaire" readonly></td>
                <td><button type="button" class="remove-row">X</button></td>
            </tr>
        </tbody>
    </table>
    <button type="button" id="addRow">Ajouter une ligne</button>
    <br><br>
    <button type="submit">Calculer la facture</button>
</form>

<script>
    // Gestion dynamique des lignes
    const table = document.getElementById('lignesTable').getElementsByTagName('tbody')[0];
    document.getElementById('addRow').addEventListener('click', function() {
        const newRow = table.rows[0].cloneNode(true);
        newRow.querySelectorAll('input, select').forEach(el => el.value = '');
        newRow.querySelector('.prix-unitaire').value = '';
        table.appendChild(newRow);
    });
    
    table.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            if (table.rows.length > 1) {
                e.target.closest('tr').remove();
            }
        }
    });
    
    table.addEventListener('change', function(e) {
        if (e.target.classList.contains('produit-select')) {
            const row = e.target.closest('tr');
            const prix = e.target.options[e.target.selectedIndex].getAttribute('data-prix');
            row.querySelector('.prix-unitaire').value = prix ? parseFloat(prix).toFixed(2) + ' €' : '';
        }
    });
</script>

<?php require_once '../../includes/footer.php'; ?>