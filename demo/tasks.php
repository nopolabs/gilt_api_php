<?php
require '../gilt_api.php';
require '../lib/cache_http_get.php';

$api_key = file_get_contents('/etc/gilt_apikey');
//$api_key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

$cache = new CacheHttpGet('cache');
$gilt = new Gilt($api_key, $cache);

function cacheProducts($products) {
  global $gilt, $cache;
  foreach ($products as $url) {
    $product = $gilt->getProduct($url);
    $cache->cache_put($url, $product->getJson());
    echo $url . "<br/>";
    flush();
  }
}

function cacheSales($sales) {
  global $gilt, $cache;
  foreach ($sales as $sale) {
    $url = $sale->getSale();
    $cache->cache_put($url, $sale->getJson());
    echo $url . "<br/>";
    flush();
    cacheProducts($sale->getProducts());
  }
}

cacheSales($gilt->getActiveSales());

