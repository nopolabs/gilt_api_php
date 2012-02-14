<?php
require __DIR__."/gilt_api.php";

$api_key = 'c73a7c168dd90eb31a76e2e9a6290890';
$gilt = new Gilt($api_key);

$req = explode('/', $_SERVER['REQUEST_URI']);
$resource = $req[count($req)-1];

function list_sales($gilt, $store_key) {
  $sales = $gilt->getActiveSales($store_key);
  $content = '';
  foreach ($sales->getSales() as $sale) {
    $content .= '<h3>' . $sale->getName() . '</h3>';
    $content .= '<p>' . $sale->getDescription() . '</p>';
    $image_urls = $sale->getImageUrls();
    $content .= '<img src="' . $image_urls['300x184']->getUrl() . '"/>';
    $content .= '<hr/>';
  }
  return $content;
}

if ($resource === Gilt::MEN) {
  $heading = 'MEN';
  $content = list_sales($gilt, Gilt::MEN);
} else if ($resource === Gilt::WOMEN) {
  $heading = 'WOMEN';
  $content = list_sales($gilt, Gilt::WOMEN);
} else if ($resource === Gilt::KIDS) {
  $heading = 'KIDS';
  $content = list_sales($gilt, Gilt::KIDS);
} else if ($resource === Gilt::HOME) {
  $heading = 'HOME';
  $content = list_sales($gilt, Gilt::HOME);
} else {
  $sales = $gilt->getActiveSales();
  $stores = array();
  foreach ($sales->getSales() as $sale) {
    if (!isset($stores[$sale->getStore()])) {
      $stores[$sale->getStore()] = array();
    }
    $stores[$sale->getStore()][] = $sale;
  }
  $heading = 'GILT';
  $content = '';
  foreach ($stores as $store_key => $store) {
    $content .= '<a href="' . $store_key . '">' . $store_key . ' has ' . count($store) . ' sales</a><br/>';
  }
}
?>
<html>
<head>
</head>
<body>
<h1><?php echo $heading; ?></h1>
<?php echo $content; ?>
</body>
</html>
