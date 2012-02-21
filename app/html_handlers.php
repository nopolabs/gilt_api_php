<?php
function home() {
  global $app, $gilt;
  $sales = $gilt->getActiveSales();
  $stores = $sales->getStores();
  $data = array(
    'base_url' => $app->request()->getRootUri() . '/',
    'heading' => 'Hello Shoppers!',
    'detail' => 'Get busy!',
    'stores' => $stores
  );
  $data['hero'] = renderPartial('hero.php', $data);
  $data['content'] = renderPartial('home.php', $data);
  $app->render('gilt.php', $data);
}

function store($store_key) {
  global $app, $gilt;
  if (!$gilt->validateStore($store_key)) {
    $app->notFound();
  }
  $store = $gilt->getActiveSales($store_key);
  $data = array(
    'base_url' => $app->request()->getRootUri() . '/',
    'heading' => $store_key,
    'detail' => 'Get busy!',
    'store_key' => $store_key,
    'store' => $store
  );
  $data['hero'] = renderPartial('hero.php', $data);
  $data['content'] = renderPartial('store.php', $data);
  $app->render('gilt.php', $data);
}

function sale($store_key, $sale_key) {
  global $app, $gilt;
  if (!$gilt->validateStore($store_key)) {
    $app->notFound();
  }
  $sale = $gilt->getSale($store_key, $sale_key);
  $imageUrls = $sale->getImageUrls();
  $products = array();
  foreach ($sale->getProducts() as $product_id) {
    $products[] = $gilt->getProduct($product_id);
  }
  $data = array(
    'base_url' => $app->request()->getRootUri() . '/',
    'heading' => $sale->getName(),
    'detail' => $sale->getDescription(),
    'image_url' => $imageUrls['300x280']->getUrl(),
    'sale' => $sale,
    'products' => $products
  );  
  $data['hero'] = renderPartial('hero.php', $data);
  $data['content'] = renderPartial('sale.php', $data);
  $app->render('gilt.php', $data);
}

function product($product_key) {
  global $app, $gilt;
  $product = $gilt->getProduct($product_key);
  $imageUrls = $product->getImageUrls();
  $data = array(
    'base_url' => $app->request()->getRootUri() . '/',
    'heading' => $product->getName(),
    'detail' => $product->getDescription(),
    'image_url' => $imageUrls['300x400']->getUrl(),
    'product' => $product
  );  
  $data['hero'] = renderPartial('hero.php', $data);
  $data['content'] = ''; 
  $app->render('gilt.php', $data);
}
