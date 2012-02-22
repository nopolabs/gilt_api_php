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

    $array = $sales->getSales();
    $sale = $array[0];
    $this->assertEquals('Casual Theme', $sale->getName());
  }

  public function test_getSale() {
    $url = 'https://api.gilt.com/v1/sales/men/sunglasses-shop-312/detail.json?apikey=test_api_key';
    $response = file_get_contents('test/data/sale_detail.json');
    $http_get = $this->getMockHttpGetOnce($url, $response);
    $gilt = new Gilt('test_api_key', $http_get);

    $sale = $gilt->getSale('men', 'sunglasses-shop-312');

    $this->assertEquals('Sunglasses Shop', $sale->getName());
    $this->assertEquals('https://api.gilt.com/v1/sales/men/sunglasses-shop-312/detail.json', $sale->getSale());
    $this->assertEquals('sunglasses-shop-312', $sale->getSaleKey());
    $this->assertEquals('men', $sale->getStore());
    $this->assertEquals('Spring’s right around the corner — and summer not long after that — which means now’s the perfect time to invest in a pair of great sunglasses. Luckily for you, we’ve compiled this collection of stylish shades by Oliver Peoples, Linda Farrow, Garrett Leight, and a host of others. Stock up, and get ready for the season ahead. ', $sale->getDescription());
    $this->assertEquals('http://www.gilt.com/sale/men/sunglasses-shop-312?utm_medium=api&utm_campaign=bagspotting.com&utm_source=salesapi', $sale->getSaleUrl());
    $this->assertEquals('2012-02-21T17:00:00Z', $sale->getBegins());
    $this->assertEquals('2012-02-23T05:00:00Z', $sale->getEnds());

    $imageUrls = $sale->getImageUrls();
    $this->assertEquals(6, count($imageUrls));
    foreach ($imageUrls as $key => $image_url) {
      $this->assertEquals($key, $image_url->getWidth() . 'x' . $image_url->getHeight());
      $this->assertEquals(1, preg_match('#http://cdn1.gilt.com/images/share/uploads/\d+/\d+/\d+/\d+/orig.jpg#', $image_url->getUrl()));
    }

    $products = $sale->getProducts();
    $this->assertEquals(93, count($products));
    foreach ($products as $product_url) {
      $this->assertEquals(1, preg_match('#https://api.gilt.com/v1/products/\d+/detail.json#', $product_url));
    }
  }

  public function test_getProduct() {
    $url = 'https://api.gilt.com/v1/products/98723426/detail.json?apikey=test_api_key';
    $response = file_get_contents('test/data/product_detail.json');
    $http_get = $this->getMockHttpGetOnce($url, $response);
    $gilt = new Gilt('test_api_key', $http_get);

    $product = $gilt->getProduct('98723426');

    $this->assertEquals('Horn Aviator Sunglasses', $product->getName());
    $this->assertEquals('https://api.gilt.com/v1/products/98723426/detail.json', $product->getProduct());
    $this->assertEquals('98723426', $product->getId());
    $this->assertEquals('Linda Farrow Luxe', $product->getBrand());
    $this->assertEquals('Linda Farrow Luxe horn acetate aviator sunglasses.  Saddle nose bridge and acetate and metal stems with cushioned ear pieces.  Embossed logo at sides of lenses and at interior temple.  Single Lens Width: 60 mm, Distance Between Lenses: 15 mm, Temple Length: 135 mm.  Comes with dust cloth and designer hard case.', $product->getDescription());
    $this->assertEquals('Acetate, Metal', $product->getMaterial());
    $this->assertEquals('Japan', $product->getOrigin());
    $this->assertEquals('http://www.gilt.com/m/public/look/?utm_medium=api&utm_campaign=bagspotting.com&utm_source=salesapi&s_id=cabe55a136143232787257042cf4347d387277926ffe81bb840d1d79fffce3e7_0_98723426', $product->getUrl());

    $imageUrls = $product->getImageUrls();
    $this->assertEquals(3, count($imageUrls));
    foreach ($imageUrls as $key => $image_url) {
      $this->assertEquals($key, $image_url->getWidth() . 'x' . $image_url->getHeight());
      $this->assertEquals(1, preg_match('#http://cdn1.gilt.com/images/share/uploads/\d+/\d+/\d+/\d+/'.$key.'.jpg#', $image_url->getUrl()));
    }

    $skus = $product->getSkus();
    $this->assertEquals(1, count($skus));
    $sku = $skus[0];
    $this->assertEquals('1373748', $sku->getId());
    $this->assertEquals('sold out', $sku->getInventoryStatus());
    $this->assertEquals('628.00', $sku->getMsrpPrice());
    $this->assertEquals('199.00', $sku->getSalePrice());

    $attributes = $sku->getAttributes();
    $this->assertEquals(1, count($attributes));
    $attribute = $attributes[0];
    $this->assertEquals('color', $attribute->name);
    $this->assertEquals('brown horn dark brown', $attribute->value);
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

