<?php
$requireLogin = false;
require_once 'init.php'; 
header('Content-Type: application/json');

$barcode = $_GET['barcode'] ?? '';

if(!$barcode){
    echo json_encode(['error' => 'No barcode provided']);
    exit;
}

$pdo = Database::connect();
$stmt = $pdo->prepare("
    SELECT * FROM products WHERE upc = ?
");
$stmt->execute([$barcode]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!$rows) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}
if(count($rows) === 1) {
    $product = $rows[0];

    $stmt = $pdo->prepare("SELECT shortTitle FROM color WHERE id = ?");
    $stmt->execute([$product['colorID']]);
    $colorName = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT name FROM sizes WHERE id = ?");
    $stmt->execute([$product['sizeID']]);
    $sizeName = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT name FROM length WHERE id = ?");
    $stmt->execute([$product['lengthID']]);
    $lengthName = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM style WHERE id = ?");
    $stmt->execute([$product['styleID']]);
    $style = $stmt->fetch(PDO::FETCH_ASSOC);
    $title = mb_substr($style['title'], 0, 12);
    if (mb_strlen($style['title']) > 12) {
        $title .= '…';
    }
    $displayName = $style['style_number'] . ' ' . $title;

    $price = $product['price'];
    if($product['is_taxable']){
        $priceVAT = $price * 1.2;
    } else {
        $priceVAT = $price;
    }
    echo json_encode([
        'name' => $displayName,
        'color' => $colorName,
        'size' => $sizeName,
        'length' => $lengthName,
        'priceNoVAT' => $price,
        'price' => $priceVAT
    ]);
} else {
    $colors = [];
    $sizes = [];
    $lengths = [];

    foreach ($rows as $product) {
        $stmt = $pdo->prepare("SELECT shortTitle FROM color WHERE id = ?");
        $stmt->execute([$product['colorID']]);
        $colorName = $stmt->fetchColumn();
        if (!in_array($product['colorID'], $colors)) $colors[] = $colorName;

        $stmt = $pdo->prepare("SELECT name FROM sizes WHERE id = ?");
        $stmt->execute([$product['sizeID']]);
        $sizeName = $stmt->fetchColumn();
        if (!in_array($product['sizeID'], $sizes)) $sizes[] = $sizeName;

        $stmt = $pdo->prepare("SELECT name FROM length WHERE id = ?");
        $stmt->execute([$product['lengthID']]);
        $lengthName = $stmt->fetchColumn();
        if (!in_array($product['lengthID'], $lengths)) $lengths[] = $lengthName;
    }
    $stmt = $pdo->prepare("SELECT * FROM style WHERE id = ?");
    $stmt->execute([$product['styleID']]);
    $style = $stmt->fetch(PDO::FETCH_ASSOC);
    $title = mb_substr($style['title'], 0, 12);
    if (mb_strlen($style['title']) > 12) {
        $title .= '…';
    }
    $displayName = $style['style_number'] . ' ' . $title;

    $price = $product['price'];
    if($product['is_taxable']){
        $priceVAT = $price * 1.2;
    } else {
        $priceVAT = $price;
    }
    echo json_encode([
        'name' => $displayName,
        'color' => $colorName,
        'size' => $sizeName,
        'length' => $lengthName,
        'priceNoVAT' => $price,
        'price' => $priceVAT

    ]);
}
?>