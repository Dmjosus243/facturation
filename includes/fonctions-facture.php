<?php

require_once dirname(__DIR__) . '/config/config.php';
require_once 'fonctions-produits.php';

function chargerFactures() {
    if (!file_exists(INVOICES_FILE)) {
        return [];
    }
    $content = file_get_contents(INVOICES_FILE);
    return json_decode($content, true) ?: [];
}

function sauvegarderFactures($factures) {
    file_put_contents(INVOICES_FILE, json_encode($factures, JSON_PRETTY_PRINT));
}

function getProchainIdFacture() {
    $factures = chargerFactures();
    if (empty($factures)) return 1;
    $ids = array_column($factures, 'id');
    return max($ids) + 1;
}

function creerFacture($client_nom, $lignes) {
    $factures = chargerFactures();
    $id = getProchainIdFacture();
    
    $total_ht = 0;
    $total_tva = 0;
    
    foreach ($lignes as &$ligne) {
        $produit = getProduitById($ligne['produit_id']);
        if (!$produit) continue;
        
        $ligne['produit_nom'] = $produit['nom'];
        $ligne['prix_unitaire_ht'] = $produit['prix_ht'];
        $ligne['tva'] = $produit['tva'];
        $ligne['total_ht'] = $ligne['quantite'] * $ligne['prix_unitaire_ht'];
        $ligne['total_tva'] = $ligne['total_ht'] * $ligne['tva'];
        $ligne['total_ttc'] = $ligne['total_ht'] + $ligne['total_tva'];
        
        $total_ht += $ligne['total_ht'];
        $total_tva += $ligne['total_tva'];
    }
    
    $total_ttc = $total_ht + $total_tva;
    
    $facture = [
        'id' => $id,
        'date' => date('Y-m-d H:i:s'),
        'client_nom' => $client_nom,
        'lignes' => $lignes,
        'total_ht' => $total_ht,
        'total_tva' => $total_tva,
        'total_ttc' => $total_ttc
    ];
    
    $factures[] = $facture;
    sauvegarderFactures($factures);
    return $id;
}

function getFactureById($id) {
    $factures = chargerFactures();
    foreach ($factures as $f) {
        if ($f['id'] == $id) {
            return $f;
        }
    }
    return null;
}

function getFacturesByDate($date) {
    $factures = chargerFactures();
    return array_filter($factures, function($f) use ($date) {
        return substr($f['date'], 0, 10) === $date;
    });
}

function getFacturesByMois($mois, $annee) {
    $factures = chargerFactures();
    return array_filter($factures, function($f) use ($mois, $annee) {
        $date = strtotime($f['date']);
        return date('m', $date) == $mois && date('Y', $date) == $annee;
    });
}

function getStatsJournalieres($date) {
    $factures = getFacturesByDate($date);
    $total_ht = array_sum(array_column($factures, 'total_ht'));
    $total_tva = array_sum(array_column($factures, 'total_tva'));
    $total_ttc = array_sum(array_column($factures, 'total_ttc'));
    return [
        'nombre' => count($factures),
        'total_ht' => $total_ht,
        'total_tva' => $total_tva,
        'total_ttc' => $total_ttc,
        'factures' => $factures
    ];
}

function getStatsMensuelles($mois, $annee) {
    $factures = getFacturesByMois($mois, $annee);
    $total_ht = array_sum(array_column($factures, 'total_ht'));
    $total_tva = array_sum(array_column($factures, 'total_tva'));
    $total_ttc = array_sum(array_column($factures, 'total_ttc'));
    return [
        'nombre' => count($factures),
        'total_ht' => $total_ht,
        'total_tva' => $total_tva,
        'total_ttc' => $total_ttc,
        'factures' => $factures
    ];
}
?>