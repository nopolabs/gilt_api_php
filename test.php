<?php

function gilt_get($url) {
  $api_key = 'c73a7c168dd90eb31a76e2e9a6290890';
  $url = $url . "?apikey=" . $api_key;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $json = curl_exec($ch);
  curl_close($ch);
  return json_decode($json);
}

$sales = gilt_get("https://api.gilt.com/v1/sales/active.json");
foreach ($sales->sales as $sale) {
  echo $sale->sale ."\n";
  if (isset($sale->products)) {
    foreach ($sale->products as $product) {
      echo $product . "\n";
      $detail = gilt_get($product);
      echo $detail->product . "\n";
      $detail2 = gilt_get($detail->product);
      if (isset($detail2->product)) {
        echo $detail2->product . "\n";
      } else {
        echo $detail2->message . "\n";
      }
      echo "---\n";
    }
  }
}
