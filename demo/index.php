<?php
require 'Slim/Slim.php';
require '../gilt_api.php';
require '../lib/http_get.php';
require 'lib/partial.php';
require 'app/json_handlers.php';
require 'app/html_handlers.php';

$api_key = 'c73a7c168dd90eb31a76e2e9a6290890';
$gilt = new Gilt($api_key, new HttpGet());

$gilt->setLogFile('/tmp/gilt.log');

$app = new Slim();

// JSON routes
$app->get('/sales/upcoming.json', 'sales_upcoming_json');
$app->get('/sales/active.json', 'sales_active_json');
$app->get('/sales/:store_key/upcoming.json', 'store_upcoming_json');
$app->get('/sales/:store_key/active.json', 'store_active_json');
$app->get('/sales/:store_key/:sale_key/detail.json', 'sale_detail_json');
$app->get('/products/:product_key/detail.json', 'product_detail_json');

// HTML routes
$app->get('/', 'home');
$app->get('/sales', 'home');
$app->get('/sales/:store_key', 'store');
$app->get('/sales/:store_key/:sale_key', 'sale');
$app->get('/products/:product_key', 'product');

$app->run();

