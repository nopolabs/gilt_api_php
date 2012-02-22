<?php
require 'gilt_api.php';
require 'lib/http_get.php';

class GiltApiTest extends PHPUnit_Framework_TestCase {

  public function test_const() {
    $this->assertEquals('0.1.0', Gilt::VERSION);
    $this->assertEquals('https://api.gilt.com/v1/', Gilt::BASE_URL_V1);
    $this->assertEquals('women', Gilt::WOMEN);
    $this->assertEquals('men', Gilt::MEN);
    $this->assertEquals('kids', Gilt::KIDS);
    $this->assertEquals('home', Gilt::HOME);
  }

  public function test_validateStore() {
    $gilt = new Gilt('test_api_key', null);
    $this->assertTrue($gilt->validateStore('women'));
    $this->assertTrue($gilt->validateStore('men'));
    $this->assertTrue($gilt->validateStore('kids'));
    $this->assertTrue($gilt->validateStore('home'));
    $this->assertFalse($gilt->validateStore('not_a_store'));
  }

  public function test_getActiveSalesUrl() {
    $gilt = new Gilt('test_api_key', null);
    $url = $gilt->getActiveSalesUrl();
    $this->assertEquals('https://api.gilt.com/v1/sales/active.json?apikey=test_api_key', $url);
  }

  public function test_getActiveSalesUrl_store() {
    $gilt = new Gilt('test_api_key', null);
    $url = $gilt->getActiveSalesUrl('men');
    $this->assertEquals('https://api.gilt.com/v1/sales/men/active.json?apikey=test_api_key', $url);
  }

  public function test_getUpcomingSalesUrl() {
    $gilt = new Gilt('test_api_key', null);
    $url = $gilt->getUpcomingSalesUrl();
    $this->assertEquals('https://api.gilt.com/v1/sales/upcoming.json?apikey=test_api_key', $url);
  }

  public function test_getUpcomingSalesUrl_store() {
    $gilt = new Gilt('test_api_key', null);
    $url = $gilt->getUpcomingSalesUrl('men');
    $this->assertEquals('https://api.gilt.com/v1/sales/men/upcoming.json?apikey=test_api_key', $url);
  }

  public function test_getSaleUrl_store_sale() {
    $gilt = new Gilt('test_api_key', null);
    $url = $gilt->getSaleUrl('men', 'test_sale');
    $this->assertEquals('https://api.gilt.com/v1/sales/men/test_sale/detail.json?apikey=test_api_key', $url);
  }

  public function test_getSaleUrl_url() {
    $gilt = new Gilt('test_api_key', null);
    $url = $gilt->getSaleUrl('https://api.gilt.com/v1/sales/men/test_sale/detail.json');
    $this->assertEquals('https://api.gilt.com/v1/sales/men/test_sale/detail.json?apikey=test_api_key', $url);
  }

  public function test_getProductUrl_product() {
    $gilt = new Gilt('test_api_key', null);
    $url = $gilt->getProductUrl('00000000');
    $this->assertEquals('https://api.gilt.com/v1/products/00000000/detail.json?apikey=test_api_key', $url);
  }

  public function test_getProductUrl_url() {
    $gilt = new Gilt('test_api_key', null);
    $url = $gilt->getProductUrl('https://api.gilt.com/v1/products/00000000/detail.json');
    $this->assertEquals('https://api.gilt.com/v1/products/00000000/detail.json?apikey=test_api_key', $url);
  }

  public function test_getActiveSales() {
    $url = 'https://test_bare_url/sales/active.json?apikey=test_api_key';
    $response = file_get_contents('test/data/active_sales.json');
    $http_get = $this->getMockHttpGetOnce($url, $response);
    $gilt = new Gilt('test_api_key', $http_get, 'https://test_bare_url/');

    $sales = $gilt->getActiveSales();

    $this->checkArrayInterfaces($sales, 75);
    $stores = $sales->getStores();
    $this->assertEquals(4, count($stores));
    $this->assertEquals(18, count($stores[Gilt::WOMEN]));
    $this->assertEquals(15, count($stores[Gilt::MEN]));
    $this->assertEquals(27, count($stores[Gilt::KIDS]));
    $this->assertEquals(15, count($stores[Gilt::HOME]));
  }

  public function test_getActiveSales_store() {
    $url = 'https://test_base_url/sales/men/active.json?apikey=test_api_key';
    $response = file_get_contents('test/data/active_sales_men.json');
    $http_get = $this->getMockHttpGetOnce($url, $response);
    $gilt = new Gilt('test_api_key', $http_get, 'https://test_base_url/');

    $sales = $gilt->getActiveSales(Gilt::MEN);

    $this->checkArrayInterfaces($sales, 18);
    $stores = $sales->getStores();
    $this->assertEquals(3, count($stores));
    $this->assertEquals(1, count($stores[Gilt::WOMEN]));
    $this->assertEquals(15, count($stores[Gilt::MEN]));
    $this->assertEquals(2, count($stores[Gilt::KIDS]));
    $this->assertFalse(array_key_exists(Gilt::HOME, $stores));
  }

  public function test_getUpcomingSales() {
    $url = 'https://test_base_url/sales/upcoming.json?apikey=test_api_key';
    $response = file_get_contents('test/data/upcoming_sales.json');
    $http_get = $this->getMockHttpGetOnce($url, $response);
    $gilt = new Gilt('test_api_key', $http_get, 'https://test_base_url/');

    $sales = $gilt->getUpcomingSales();

    $this->checkArrayInterfaces($sales, 131);
    $stores = $sales->getStores();
    $this->assertEquals(4, count($stores));
    $this->assertEquals(44, count($stores[Gilt::WOMEN]));
    $this->assertEquals(33, count($stores[Gilt::MEN]));
    $this->assertEquals(33, count($stores[Gilt::KIDS]));
    $this->assertEquals(21, count($stores[Gilt::HOME]));
  }

  public function test_getSale() {
  }

  public function test_getProduct() {
  }

  ////

  private function getMockHttpGetOnce($url, $response) {
    $http_get = $this->getMock('HttpGet');
    $http_get->expects($this->once())
             ->method('get')
             ->with($url)
             ->will($this->returnValue($response));
    return $http_get;
  }

  private function checkArrayInterfaces($target, $count) {
    $this->assertEquals($count, count($target));
    $i = 0;
    foreach ($target as $el) {
      $this->assertEquals($target[$i++], $el);
    }
    $this->assertEquals($count, $i);
  }

}

