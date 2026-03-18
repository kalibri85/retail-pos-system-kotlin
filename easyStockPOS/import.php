<?php
require_once 'init.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
if(!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK){
    die('CSV upload failed');
}

$pdo = Database::connect();
$handle = fopen($_FILES['csv']['tmp_name'], 'r');
if(!$handle) die('Cannot open file');

$header = fgetcsv($handle);

$bSize = 1000;
$batch = [];

$created = 0;
$updated = 0;
$skipped = 0;

$styleCache = [];
$colorCache = [];
$lengthCache = [];
$brandCache = [];

function getOrCreateCached($pdo, $table, $field, $value, &$cache, $extra = []){
    if(isset($cache[$value])) return $cache[$value];

    $sql = $pdo->prepare("SELECT id FROM {$table} WHERE {$field} = ? LIMIT 1");
    $sql->execute([$value]);
    $row = $sql->fetch();
    if($row){
        $cache[$value] = $row['id'];
        return $row['id'];
    }

    $columns = array_merge([$field], array_keys($extra));
    $placeholders = '('.implode(',', array_fill(0, count($columns), '?')).')';
    $values = array_merge([$value], array_values($extra));

    $sql = $pdo->prepare("INSERT INTO {$table} (".implode(',',$columns).") VALUES {$placeholders}");
    $sql->execute($values);
    $id = $pdo->lastInsertId();
    $cache[$value] = $id;
    return $id;
}

function processBatch($batch, $pdo, &$created, &$updated, &$skipped, &$styleCache, &$sizeCache, &$colorCache, &$lengthCache, &$brandCache){
    $insertData = [];

    foreach($batch as $row){
        [
        $styleNumber,
        $colorShortcode,
        $lengthName,
        $sizeName,
        $brandName,
        $colorName,
        $title,
        $upc,
        $isTaxable,
        $price,
        $qty
    ] = $row;

        $isTaxable = (int)$isTaxable;
        $title = mb_substr($title, 0, 255);
        $qty = trim($qty);
        $qty = is_numeric($qty) ? (int)$qty : 0;
        
        $styleID = getOrCreateCached($pdo, 'style', 'style_number', $styleNumber, $styleCache, ['title'=>$title]);
        $sizeID = getOrCreateCached($pdo, 'sizes', 'name', $sizeName, $sizeCache);
        $colorID = getOrCreateCached($pdo, 'color', 'colorName', $colorName, $colorCache,  ['shortTitle'=>$colorShortcode]);
        $lengthID = getOrCreateCached($pdo, 'length', 'name', $lengthName, $lengthCache);
        $brandID = getOrCreateCached($pdo, 'brand', 'name', $brandName, $brandCache);

        $insertData[] = [
            'styleID'=>$styleID,
            'sizeID'=>$sizeID,
            'colorID'=>$colorID,
            'lengthID'=>$lengthID,
            'brandID'=>$brandID,
            'upc'=>$upc,
            'is_taxable'=>$isTaxable,
            'price'=>$price,
            'qty_inventory'=>$qty
        ];
    }
    if(!empty($insertData)){
        $placeholders = [];
        $values = [];
        foreach($insertData as $data){
            $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $values = array_merge($values, array_values($data));
        }
        $sql = "INSERT INTO products (styleID, sizeID, colorID, lengthID, brandID, upc, is_taxable, price, qty_inventory)
            VALUES ".implode(',', $placeholders)."ON DUPLICATE KEY UPDATE price = VALUES(price), qty_inventory = VALUES(qty_inventory)";
        $sql = $pdo->prepare($sql);
        $sql->execute($values);
        
        $affected = $sql->rowCount();
        $created += count($insertData);
        $updated += $affected - count($insertData);

    }
}

while(($row = fgetcsv($handle)) !== false){
    $batch[] = $row;
    if(count($batch) >= $bSize){
        processBatch($batch, $pdo, $created, $updated, $skipped, $styleCache, $sizeCache, $colorCache, $lengthCache, $brandCache);
        $batch = [];
    }
}
if(!empty($batch)){
    processBatch($batch, $pdo, $created, $updated, $skipped, $styleCache, $sizeCache, $colorCache, $lengthCache, $brandCache);
}    
fclose($handle);

//Dry run import
echo "<h3>Dry run import info</h3>";
echo "<div>New products: {$created}</div>";
echo "<div>Will be updated: {$updated}</div>";
echo "<div>Skipped: {$skipped}</div>";
echo "<p><a href='products.php'>Back to products</a></p>";
exit;
?>