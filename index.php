<?php
require __DIR__."/gilt_api.php";

$api_key = 'c73a7c168dd90eb31a76e2e9a6290890';
$gilt = new Gilt($api_key);
$sales = $gilt->getActiveSales();
?>
<html>
<head>
</head>
<body>
<?php echo $sales->getJson(); ?>
</body>
</html>
