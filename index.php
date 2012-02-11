<?php
//ini_set('memory_limit', '-1');

require __DIR__."/gilt_api.php";

$api_key = 'c73a7c168dd90eb31a76e2e9a6290890';
$gilt = new Gilt($api_key);
$sales = $gilt->getActiveSales(Gilt::MEN);
//$resty = new Resty();
//$google = $resty->get('http://google.com');
?>
<html>
<head>
</head>
<body>
<?php 
var_dump($sales);
echo $sales->getJson(); 
?>
</body>
</html>
