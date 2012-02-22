<?php
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

