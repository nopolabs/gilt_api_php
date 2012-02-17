<?php
require 'gilt_api.php';
require 'Slim/Slim.php';

$api_key = 'c73a7c168dd90eb31a76e2e9a6290890';
$gilt = new Gilt($api_key);

$app = new Slim();
$app->get('/', 'gilt');
$app->get('/product/:product_key', 'product');
$app->get('/:store_key', 'store');
$app->get('/:store_key/:sale_key', 'sale');
function gilt() {
    global $app, $gilt;
    $sales = $gilt->getActiveSales();
    $app->render('sales.php', array('sales' => $sales));
}
function store($store_key) {
    global $app, $gilt;
    $sales = $gilt->getActiveSales($store_key);
    $app->render('sales.php', array('sales' => $sales));
}
function sale() {
    global $app, $gilt;
    $sale = $gilt->getSale($store_key, $sale_key);
    $app->render('sale.php', array('sale' => $sale));
}
function product($product_key) {
    global $app, $gilt;
    $product = $gilt->getProduct($product_key);
    $app->render('product.php', array('product' => $product));
}
$app->run();

