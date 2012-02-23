<?php
require 'http_get.php';

$http_get = new HttpGet();

$sha1 = sha1('https://api.gilt.com/v1/sales/men/active.json?apikey=c73a7c168dd90eb31a76e2e9a6290890');

$data = $http_get->cache_get($sha1);

echo 'DATA:' . $data;

$http_get->cache_put($sha1, 'hello world');

$data = $http_get->cache_get($sha1);

echo 'DATA:' . $data;

