<?php
require __DIR__."/gilt_api.php";

$api_key = 'c73a7c168dd90eb31a76e2e9a6290890';
$gilt = new Gilt($api_key);
$sales = $gilt->getActiveSales(Gilt::MEN);
?>
<html>
<head>
</head>
<body>
<?php 
foreach ($sales->getSales() as $sale) {
?>
<p><?php echo $sale->getName(); ?></p>
<p><?php echo $sale->getSale(); ?></p>
<?php
}
?>
</body>
</html>
