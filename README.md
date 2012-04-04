Gilt API PHP
============

gilt_api_php provides a PHP client for the Gilt Groupe APIs.

The Gilt Groupe APIs give developers the ability to build applications that display information about Gilt Groupe's upcoming sales and the products available in those sales.

Requirements
------------

PHP 5.2.7+

PHPUnit 3.4.5+

Documentation
-------------
  * http://nopolabs.github.com/gilt_api_php/
  * http://nopolabs.github.com/gilt_api_php/apidocs/index.html
  * https://dev.gilt.com/

Usage
-----
    require 'gilt_api.php';
    require 'lib/http_get.php';

    $api_key = <your_gilt_api_key>;
    $http_get = new HttpGet();
    $gilt = new Gilt($api_key, $http_get);
    $sales = $gilt->getActiveSales();

Demo
----
The demo PHP app allows browsing of Gilt sales and products.

The app was built using the Slim framework and the Twitter bootstrap library.

Edit demo/index.php to set your Gilt API key.

