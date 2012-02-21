<?php
require 'gilt_api.php';

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
    $gilt = new Gilt('test_api_key');
    $this->assertTrue($gilt->validateStore('women'));
    $this->assertTrue($gilt->validateStore('men'));
    $this->assertTrue($gilt->validateStore('kids'));
    $this->assertTrue($gilt->validateStore('home'));
    $this->assertFalse($gilt->validateStore('not_a_store'));
  }

  public function test_getGiltJson_array() {
    $rest_api = $this->getMockRestApi();
    $rest_api->expects($this->once())
             ->method('getPath')
             ->will($this->returnValue('{getPath}'));
    $gilt = new Gilt('test_api_key', $rest_api);
    $json = $gilt->getGiltJson(array());
    $this->assertEquals('{getPath}', $json);
  }

  public function test_getGiltJson_string() {
    $rest_api = $this->getMockRestApi();
    $rest_api->expects($this->once())
             ->method('getUrl')
             ->will($this->returnValue('{getUrl}'));
    $gilt = new Gilt('test_api_key', $rest_api);
    $json = $gilt->getGiltJson('string');
    $this->assertEquals('{getUrl}', $json);
  }

  public function test_getActiveSales() {
    $response = file_get_contents('test/data/active_sales.json');
    $rest_api = $this->getMockRestApi();
    $rest_api->expects($this->once())
             ->method('getPath')
             ->with(array('sales', 'active.json'))
             ->will($this->returnValue($response));
    $gilt = new Gilt('test_api_key', $rest_api);
    $sales = $gilt->getActiveSales();
    $this->checkArrayInterfaces($sales, 75);
    $stores = $sales->getStores();
    $this->assertEquals(4, count($stores));
    $this->assertEquals(18, count($stores[Gilt::WOMEN]));
    $this->assertEquals(15, count($stores[Gilt::MEN]));
    $this->assertEquals(27, count($stores[Gilt::KIDS]));
    $this->assertEquals(15, count($stores[Gilt::HOME]));
  }

  public function test_getActiveSales_men() {
    $response = file_get_contents('test/data/active_sales_men.json');
    $rest_api = $this->getMockRestApi();
    $rest_api->expects($this->once())
             ->method('getPath')
             ->with(array('sales', Gilt::MEN, 'active.json'))
             ->will($this->returnValue($response));
    $gilt = new Gilt('test_api_key', $rest_api);
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
    $response = file_get_contents('test/data/upcoming_sales.json');
    $rest_api = $this->getMockRestApi();
    $rest_api->expects($this->once())
             ->method('getPath')
             ->with(array('sales', 'upcoming.json'))
             ->will($this->returnValue($response));
    $gilt = new Gilt('test_api_key', $rest_api);
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

  private function getMockRestApi() {
    $rest_api = $this->getMockBuilder('RestApi')
                     ->disableOriginalConstructor()
                     ->getMock();
    return $rest_api;
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

