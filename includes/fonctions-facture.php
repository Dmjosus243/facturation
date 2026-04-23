<?php
// includes/fonctions-factures.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/fonctions-produits.php';

function load_invoices() {
    return read_json(INVOICES_FILE);
}

function save_invoices($invoices) {
    return write_json_atomic(INVOICES_FILE, $invoices);
}

function create_invoice($items, $meta = []) {
    // items: array of ['product_id','barcode','name','qty','unit_price','vat_rate']
    $total_ht = 0.0; $total_vat = 0.0;
    foreach ($items as &$it) {
        $line_ht = $it['unit_price'] * intval($it['qty']);
        $line_vat = $line_ht * ($it['vat_rate']/100.0);
        $it['line_ht'] = round($line_ht,2);
        $it['line_vat'] = round($line_vat,2);
        $it['line_ttc'] = round($line_ht + $line_vat,2);
        $total_ht += $line_ht;
        $total_vat += $line_vat;
        // decrement stock best-effort
        decrement_product_stock($it['product_id'], $it['qty']);
    }
    $invoice = [
        'id' => uniqid('inv_'),
        'created_at' => date('c'),
        'items' => $items,
        'total_ht' => round($total_ht,2),
        'total_vat' => round($total_vat,2),
        'total_ttc' => round($total_ht + $total_vat,2),
        'meta' => $meta
    ];
    $invoices = load_invoices();
    $invoices[] = $invoice;
    if (!save_invoices($invoices)) return false;
    return $invoice['id'];
}

function load_invoice($id) {
    foreach (load_invoices() as $inv) {
        if ($inv['id'] === $id) return $inv;
    }
    return null;
}

function decrement_product_stock($product_id, $qty) {
    $products = load_products();
    $changed = false;
    foreach ($products as &$p) {
        if ($p['id'] === $product_id) {
            $p['stock'] = max(0, intval($p['stock']) - intval($qty));
            $changed = true;
            break;
        }
    }
    if ($changed) return save_products($products);
    return false;
}
