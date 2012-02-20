<?php
require 'Slim/Slim.php';
require 'gilt_view.php';
require 'gilt_api.php';

$api_key = 'c73a7c168dd90eb31a76e2e9a6290890';
$gilt = new Gilt($api_key);

$partial = new Slim_View();
$partial->setTemplatesDirectory('partials');

function renderPartial($template, $data) {
  global $partial;
  $partial->setData($data);
  return $partial->render($template);
}

$app = new Slim();

$app->get('/sales/upcoming.json', 'sales_upcoming_json');
$app->get('/sales/active.json', 'sales_active_json');
$app->get('/sales/:store_key/upcoming.json', 'store_upcoming_json');
$app->get('/sales/:store_key/active.json', 'store_active_json');
$app->get('/sales/:store_key/:sale_key/detail.json', 'sale_detail_json');
$app->get('/product/:product_key/detail.json', 'product_detail_json');

$app->get('/', 'gilt');
$app->get('/sales/:store_key', 'store');
$app->get('/sales/:store_key/:sale_key', 'sale');
$app->get('/product/:product_key', 'product');

function gilt() {
  global $app, $gilt;
  $sales = $gilt->getActiveSales();
  $stores = $sales->getStores();
  $app->render('gilt.php', array(
    'hero' => renderPartial('hero.php', array(
      'heading' => 'Hello Shoppers!', 
      'detail' => 'Get busy!'
    )),
    'content' => renderPartial('home.php', array(
      'stores' => $stores
    ))
  ));
}

function store($store_key) {
  global $app, $gilt;
  if (!$gilt->validateStore($store_key)) {
    $app->notFound();
  }
  $store = $gilt->getActiveSales($store_key);
  $app->render('gilt.php', array(
    'hero' => renderPartial('hero.php', array(
      'heading' => 'Hello Shoppers!', 
      'detail' => 'Get busy!'
    )),
    'content' => renderPartial('sales.php', array(
      'store' => $store
    ))
  ));
}

function sale($store_key, $sale_key) {
  global $app, $gilt;
  if (!$gilt->validateStore($store_key)) {
    $app->notFound();
  }
  $sale = $gilt->getSale($store_key, $sale_key);
  $app->render('sale.php', array('sale' => $sale));
}

function product($product_key) {
  global $app, $gilt;
  $product = $gilt->getProduct($product_key);
  $app->render('product.php', array('product' => $product));
}

function sales_upcoming_json() {
  global $app, $gilt;
  $sales = $gilt->getUpcomingSales();
  $app->render('json.php', array('json' => $sales->getJson()));
}

function store_upcoming_json($store_key) {
  global $app, $gilt;
  if (!$gilt->validateStore($store_key)) {
    $app->notFound();
  }
  $sales = $gilt->getUpcomingSales($store_key);
  $app->render('json.php', array('json' => $sales->getJson()));
}

function sales_active_json() {
  global $app, $gilt;
  $sales = $gilt->getActiveSales();
  $app->render('json.php', array('json' => $sales->getJson()));
}

function store_active_json($store_key) {
  global $app, $gilt;
  if (!$gilt->validateStore($store_key)) {
    $app->notFound();
  }
  $sales = $gilt->getActiveSales($store_key);
  $app->render('json.php', array('json' => $sales->getJson()));
}

function sale_detail_json($store_key, $sale_key) {
  global $app, $gilt;
  if (!$gilt->validateStore($store_key)) {
    $app->notFound();
  }
  $sale = $gilt->getSale($store_key, $sale_key);
  $app->render('json.php', array('json' => $sale->getJson()));
}

function product_detail_json($product_key) {
  global $app, $gilt;
  $product = $gilt->getProduct($product_key);
  $app->render('json.php', array('product' => $product));
}

$app->run();

