<?php 
$stores = array();
$sales = $this->data['sales'];
foreach ($sales as $sale) {
  $store_key = $sale->getStore();
  if (!array_key_exists($store_key, $stores)) {
    $stores[$store_key] = array();
  }
  $stores[$store_key][] = $sale;
}
foreach ($stores as $store_key => $store) {
  echo "<h2>" . $store_key . "</h2>";
  echo count($store) . " sales<br/>";
}
