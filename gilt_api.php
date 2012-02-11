<?php
/**
 * Gilt API PHP
 *
 *  $api_key = <your_gilt_api_key>;
 *  $gilt = new Gilt($api_key);
 *  $sales = $gilt->getActiveSales();
 */
 
require __DIR__."/lib/Resty.php";
 
class Gilt {

	/**
	 * The version of this lib
	 */
	const VERSION = '0.1.0';

  const WOMEN = 'women';
  const MEN   = 'men';
  const KIDS  = 'kids';
  const HOME  = 'home';

	private $api_key;
	private $resty;
	
  public function __construct($api_key, $resty = new Resty()) {
    $this->api_key = $api_key;
    $this->resty = $resty;
    $this->resty->setBaseURL('https://api.gilt.com/v1/');
  }

  private buildPath($first, $last, $middle = null) {
    if (empty($middle)) {
      $path = $first.'/'.$last.'.json';
    } else {
      $path = $first.'/'.$middle.'/'.$last.'.json';
    }
    return $path;
  }
  
  private getGiltData($path) {
    $json = $this->resty->get($path);
    return GiltData::fromJson($json);
  }
    
  private function getSales($sales_type, $store_key) {
    $path = buildPath('sales', $sales_type, $store_key);
    $gilt_data = getGiltData($path);
    return new Sales($gilt_data);
  }
  
  public function getActiveSales($store_key = null) {
    return $this->getSales('active', $store_key)
  }
  
  public function getUpcomingSales($store_key = null) {
    return $this->getSales('upcoming', $store_key)
  }
  
  public function getSale($store_key, $sale_key) {
    $path = buildPath('sales', $sale_key, $store_key);
    $gilt_data = getGiltData($path);
    return new Sale($gilt_data);
  }
  
  public function getProduct($product_id) {
    $path = buildPath('products', 'detail', $product_id);
    $gilt_data = getGiltData($path);
    return new Product($gilt_data);
  }
}

class GiltData {

  private $json;
  private $data;
  
  static function fromJson($json) {
  	$gilt_data = new GiltData();
  	$gilt_data->setJson($json);
  	return $gilt_data;
  }

  static function fromData($data) {
  	$gilt_data = new GiltData();
  	$gilt_data->setData($data);
  	return $gilt_data;
  }

  protected function setJson($json) {
    $this->json = $json;
  }
  
  protected function setData($data) {
    $this->data = $data;
  }
  
  public function getJson() {
    if (!isset($this->json)) {
      $this->json = json_encode($this->object);
    }
    return $this->json;
  }
  
  function getData() {
    if (!isset($this->data)) {
      $this->data = json_decode($this->json);
    }
    return $this->data;
  }
}

class GiltDataHolder {
	  
  private $gilt_data;

  function __construct($gilt_data) {
    $this->gilt_data = $gilt_data;
  }
  
  function getJson() {
  	return $this->gilt_data->getJson();
  }
  
  function getData() {
  	return $this->gilt_data->getData();
  }
  
  public function getString($key) {
  	$data = $this->getData();
    if (isset($data[$key])) {
      $value = $data[$key];
    } else {
      $value = '';
    }
    return $value;
  }  
  
  public function getNumber($key) {
  	$data = $this->getData();
    if (isset($data[$key])) {
      $value = $data[$key];
    } else {
      $value = 0;
    }
    return $value;
  }  
  
  public function getArray($key) {
  	$data = $this->getData();
    if (isset($data[$key])) {
      $value = $data[$key];
    } else {
      $value = array();
    }
    return $value;
  }  
  
  public function getArrayString($key, $subKey) {
  	$data = $this->getData();
    if (isset($data[$key])) {
	    if (isset($data[$key][$subKey])) {
	      $value = $data[$key][$subKey];
	    } else {
	      $value = '';
	    }
    } else {
      $value = '';
    }
    return $value;
  }  
  
}

class Sales extends GiltDataHolder {

  private $sales;
  
  public function getSales() {
    if (!isset($this->sales)) {
      $this->sales = array();
      for ($this->getData() as $data) {
        $gilt_data = GiltData::fromData($data);
        $this->sales[] = new Sale($gilt_data);
      }
    }
    return $this->sales;
  }
  
  public function getJson() {
    return $this->getJson();
  } 
}

class Sale extends GiltDataHolder {
  
  /**
   * Sale name
   * @var string
   */
  public getName() {
    return $this->getString('name');
  }
  
  /**
   * URL to single sale object
   * @var string
   */
  public getSale() {
    return $this->getString('sale');
  }
  
  /**
   * String 	unique identifier for sale
   * @var string
   */
  public getSaleKey() {
    return $this->getString('sale_key');
  }
  
  /**
   * Store key
   * @var string
   */
  public getStore() {
    return $this->getString('store');
  }
  
  /**
   * A description of the sale's theme or brand (optional)
   * @var string
   */
  public getDescription() {
    return $this->getString('description');
  }
  
  /**
   * Permalink to sale website
   * @var string
   */
  public getSaleUrl() {
    return $this->getString('sale_url');
  }
  
  /**
   * ISO8601-formatted time for beginning of sale
   * @var string
   */
  public getBegins() {
    return $this->getString('begins');
  }
  
  /**
   * ISO-8601-formatted time for end of sale (optional)
   * @var string
   */
  public getEnds() {
    return $this->getString('ends');
  }
  
  /**
   * See image URLs
   * @var array
   */
  public getImageUrls() {
    $data = $this->getArray('image_urls');
    $image_urls = array();
    foreach ($data as $key => $value) {
      $image_urls[$key] = new ImageUrl(GiltData::fromData($value));
    }
    return $image_urls;
  }
  
  /**
   * List of URLs to individual product objects (optional, active sales only)
   * @var array
   */
  public getProducts() {
    return $this->getArray('products');
  }
  
}

class Product extends GiltDataHolder {

  /**
   * Product name
   * @var string
   */
  public getName() {
    return $this->getString('name');
  }
  
  /**
   * URL to product object
   * @var string
   */
  public getProduct() {
    return $this->getString('product');
  }
  
  /**
   * Unique identifier for product
   * @var int
   */
  public getId() {
    return $this->getNumber('id');
  }
  
  /**
   * Brand name
   * @var string
   */
  public getBrand() {
    return $this->getString('brand');
  }
  
  /**
   * Link to product detail page where item can be purchased
   * @var string
   */
  public getUrl() {
    return $this->getString('url');
  }
  
  /**
   * See Image URLs
   * @var array
   */
  public getImageUrls() {
    return $this->getArray('image_urls');
  }
  
  /**
   * See SKUs
   * @var array
   */
  public getSkus() {
    $data = $this->getArray('skus');
    $skus = array();
    for ($data as $value) {
    	$skus[] = new Sku(GiltData::fromData($value));
    }
    return $skus;
  }
  
  /**
   * An array containing following fields: description, fit_notes, material, care_instructions, origin
   * @var array
   */
  public getContent() {
    return $this->getArray('content');
  }
  
  /**
   * Product description (optional)
   * @var string
   */
  public getDescription() {    
    return $this->getArrayString('content', 'description');
  }

  /**
   * Sizing information (optional)
   * @var string
   */
  public getFitNotes() {    
    return $this->getArrayString('content', 'fit_notes');
  }

  /**
   * Materials list (optional)
   * @var string
   */
  public getMaterial() {    
    return $this->getArrayString('content', 'material');
  }

  /**
   * Additional care information (optional)
   * @var string
   */
  public getCareInstructions() {    
    return $this->getArrayString('content', 'care_instructions');
  }

  /**
   * Place of manufacture (optional)
   * @var string
   */
  public getOrigin() {    
    return $this->getArrayString('content', 'origin');
  }

}

class ImageUrl extends GiltDataHolder {

  /**
   * The URL to the image
   * @var string
   */
  public getUrl() {    
    return $this->getString('url');
  }
  
  /**
   * The width of the image
   * @var int
   */
  public getWidth() {    
    return $this->getNumber('width');
  }
  
  /**
   * The height of the image
   * @var int
   */
  public getHeight() {    
    return $this->getNumber('height');
  }

}

class Sku extends GiltDataHolder {
	
  /**
   * SKU id
   * @var int
   */
  public getId() {    
    return $this->getNumber('id');
  }

  /**
   * Describes product availability. One of: "sold out", "for sale", "reserved"
   * @var string
   */
  public getInventoryStatus() {
  	return $this->getString('inventory_status');
  }

  /**
   * MSRP price of SKU in US Dollars
   * @var string
   */
  public getMsrpPrice() {
  	return $this->getString('msrp_price');
  }

sale_price 	String 	Sale price of SKU in US Dollars
   * @var string
  /**
   * Sale price of SKU in US Dollars
   * @var string
   */
  public getSalePrice() {
  	return $this->getString('sale_price');
  }
  
  /**
   * If absent, standard Gilt.com shipping policy and any resulting charges apply. 
   * If present, standard shipping charge is overridden by amount listed here in US Dollars.
   * @var string
   */
  public getShippingSurcharge() {
  	return $this->getString('shipping_surcharge');
  }

  /**
   * Name/value pairs of SKU attributes like "color" and "size"
   * @var array
   */
  public getAttributes() {
  	return $this->getString('attributes');
  }

}