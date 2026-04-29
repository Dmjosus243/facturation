<?php

require_once dirname(__DIR__) . '/config/config.php';

function chargerProduits() {
    if (!file_exists(PRODUCTS_FILE)) {
        return [];
    }
    $content = file_get_contents(PRODUCTS_FILE);
    return json_decode($content, true) ?: [];
}

function sauvegarderProduits($produits) {
    file_put_contents(PRODUCTS_FILE, json_encode($produits, JSON_PRETTY_PRINT));
}

function getProchainIdProduit() {
    $produits = chargerProduits();
    if (empty($produits)) return 1;
    $ids = array_column($produits, 'id');
    return max($ids) + 1;
}

function ajouterProduit($nom, $prix_ht, $code_barre = '', $tva = TVA_RATE) {
    $produits = chargerProduits();
    $id = getProchainIdProduit();
    $produits[] = [
        'id' => $id,
        'nom' => $nom,
        'prix_ht' => floatval($prix_ht),
        'code_barre' => $code_barre,
        'tva' => floatval($tva)
    ];
    sauvegarderProduits($produits);
    return $id;
}

function modifierProduit($id, $nom, $prix_ht, $code_barre = '', $tva = TVA_RATE) {
    $produits = chargerProduits();
    foreach ($produits as &$p) {
        if ($p['id'] == $id) {
            $p['nom'] = $nom;
            $p['prix_ht'] = floatval($prix_ht);
            $p['code_barre'] = $code_barre;
            $p['tva'] = floatval($tva);
            sauvegarderProduits($produits);
            return true;
        }
    }
    return false;
}

function supprimerProduit($id) {
    $produits = chargerProduits();
    $nouveaux = array_filter($produits, function($p) use ($id) {
        return $p['id'] != $id;
    });
    if (count($nouveaux) != count($produits)) {
        sauvegarderProduits(array_values($nouveaux));
        return true;
    }
    return false;
}

function getProduitById($id) {
    $produits = chargerProduits();
    foreach ($produits as $p) {
        if ($p['id'] == $id) {
            return $p;
        }
    }
    return null;
}

function getProduitByCodeBarre($code) {
    $produits = chargerProduits();
    foreach ($produits as $p) {
        if ($p['code_barre'] === $code) {
            return $p;
        }
    }
    return null;
}

function getProduitsListe() {
    return chargerProduits();
}
?>