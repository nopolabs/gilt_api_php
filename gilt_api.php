<?php
/**
 * Gilt API PHP
 *
 * <pre>
 * $api_key = your_gilt_api_key;
 * $http_get = new HttpGet();
 * $gilt = new Gilt($api_key, $http_get);
 * $sales = $gilt->getActiveSales();
 * </pre>
 */
class Gilt {

  /**
   * The version of this lib
   */
  const VERSION = '0.1.0';

  const BASE_URL_V1 = 'https://api.gilt.com/v1/';

  const WOMEN = 'women';
  const MEN   = 'men';
  const KIDS  = 'kids';
  const HOME  = 'home';

  private $api_key;
  private $base_url;
  private $http_get;
  private $log_file;
  
  public function __construct($api_key, $http_get, $base_url=Gilt::BASE_URL_V1) {
    $this->api_key = $api_key;
    $this->http_get = $http_get;
    $this->base_url = $base_url;
  }

  /**
   * Verify that $store_key is a valid store key.
   * @return boolean
   */
  public function validateStore($store_key) {
    return in_array($store_key, array(self::WOMEN, self::MEN, self::KIDS, self::HOME));
  }

  /**
   * Get the URL for the JSON document describing all active sales.
   * Optional store_key selects sales for a single store if provided.
   * @return string
   */
  public function getActiveSalesUrl($store_key = null) {
    return $this->buildUrl('sales', $store_key, 'active.json');
  }

  /**
   * Get the URL for the JSON document describing upcoming sales.
   * Optional store_key selects sales for a single store if provided.
   * @return string
   */
  public function getUpcomingSalesUrl($store_key = null) {
    return $this->buildUrl('sales', $store_key, 'upcoming.json');
  }

  /**
   * Get the URL for the JSON document describing a specific active or upcoming sale.
   * @return string
   */
  public function getSaleUrl($store_key, $sale_key) {
    return $this->buildUrl('sales', $store_key, $sale_key, 'detail.json');
  }

  /**
   * Get the URL for the JSON document describing a specific product.
   * @return string
   */
  public function getProductUrl($product_id) {
    return $this->buildUrl('products', $product_id, 'detail.json');
  }

  /**
   * Get active Sales.
   * Optional store_key selects sales for a single store if provided.
   * @return Sales
   */
  public function getActiveSales($store_key = null) {
    $url = $this->getActiveSalesUrl($store_key);
    $json = $this->getGiltJson($url);
    return new Sales($json);
  }
  
  /**
   * Get upcoming Sales.
   * Optional store_key selects sales for a single store if provided.
   * @return Sales
   */
  public function getUpcomingSales($store_key = null) {
    $url = $this->getUpcomingSalesUrl($store_key);
    $json = $this->getGiltJson($url);
    return new Sales($json);
  }
  
  /**
   * Get a specific Sale.
   * @return Sale
   */
  public function getSale($store_key, $sale_key) {
    $url = $this->getSaleUrl($store_key, $sale_key);
    $json = $this->getGiltJson($url);
    return new Sale($json);
  }
  
  /**
   * Get a specific Product.
   * @return Product
   */
  public function getProduct($product_id) {
    $url = $this->getProductUrl($product_id);
    $json = $this->getGiltJson($url);
    return new Product($json);
  }

  /**
   * Specify the location of the log file.
   * If specified API request and responses will be recorded in this file.
   */
  public function setLogFile($log_file) {
    $this->log_file = $log_file;
  }

  protected function log($msg) {
    if (isset($this->log_file)) {
      file_put_contents($this->log_file, $msg."\n"); //, FILE_APPEND);
    }
  }

  protected function getGiltJson($url) {
    $this->log('URL: ' . $url);
    $json = $this->http_get->get($url);
    $this->log('RSP: ' . $json);
    return json_decode($json);
  }
    
  protected function buildUrl() {
    $path_els = array_filter(func_get_args());
    $url_pattern = '#' . $this->base_url . '.*\.json#';
    if (isset($path_els[1]) && preg_match($url_pattern, $path_els[1])) {
      $url = $path_els[1];
    } else {
      $url = $this->base_url;
      $sep = '';
      foreach ($path_els as $el) {
        $url .= $sep . $el;
        $sep = '/';
      }
    }
    return $url . '?' . 'apikey=' . $this->api_key;
  }
}

/**
 * Base class for Gilt API objects.
 */
class GiltData {

  private $json;
  private $obj;
  
  public function __construct($data) {
    if (is_array($data)) {
      $data = $data[0];
    }
    if (is_object($data)) {
      $this->obj = $data;
      $this->json = null;
    } else {
      $this->obj = null;
      $this->json = $data;
    }
  }

  /**
   * Get JSON representing this object.
   * @return string
   */
  public function getJson() {
    if (!isset($this->json)) {
      $this->json = json_encode($this->obj);
    }
    return $this->json;
  }
  
  protected function getObj() {
    if (!isset($this->obj)) {
      $this->obj = json_decode($this->json);
    }
    return $this->obj;
  }
}

/**
 * A set of Sales with implementing ArrayAccess, Iterator, and Countable interfaces. 
 */
class Sales extends GiltData implements ArrayAccess, Iterator, Countable {

  private $sales;
  private $stores;
  
  public function __construct($data) {
    parent::__construct($data);
    $this->sales = array();
    $this->stores = array();
    $obj = $this->getObj();
    foreach ($obj->sales as $data) {
      $sale = new Sale($data);
      array_push($this->sales, $sale);
      $store_key = $sale->getStore();
      if (!array_key_exists($store_key, $this->stores)) {
        $this->stores[$store_key] = array();
      }
      array_push($this->stores[$store_key], $sale);
    }
  }

  /**
   * Get an array of Sale objects.
   * @return array
   */
  public function getSales() {
    return $this->sales;
  }

  /**
   * Get a map of store_key => Sales
   * @return array
   */
  public function getStores() {
    return $this->stores;
  }

  /**
   * @see http://php.net/manual/en/class.arrayaccess.php
   */
  public function offsetSet($offset, $value) {
    if (is_null($offset)) {
      $this->sales[] = $value;
    } else {
      $this->sales[$offset] = $value;
    }
  }

  /**
   * @see http://php.net/manual/en/class.arrayaccess.php
   */
  public function offsetExists($offset) {
    return isset($this->sales[$offset]);
  }

  /**
   * @see http://php.net/manual/en/class.arrayaccess.php
   */
  public function offsetUnset($offset) {
    unset($this->sales[$offset]);
  }

  /**
   * @see http://php.net/manual/en/class.arrayaccess.php
   */
  public function offsetGet($offset) {
    return isset($this->sales[$offset]) ? $this->sales[$offset] : null;
  }

  /**
   * @see http://www.php.net/manual/en/class.iterator.php
   */
  public function rewind() {
    reset($this->sales);
  }

  /**
   * @see http://www.php.net/manual/en/class.iterator.php
   */
  public function current() {
    return current($this->sales);
  }

  /**
   * @see http://www.php.net/manual/en/class.iterator.php
   */
  public function key() {
    return key($this->sales);
  }

  /**
   * @see http://www.php.net/manual/en/class.iterator.php
   */
  public function next() {
    return next($this->sales);
  }

  /**
   * @see http://www.php.net/manual/en/class.iterator.php
   */
  public function valid() {
    return $this->current() !== false;
  }   

  /**
   * @see http://us3.php.net/manual/en/class.countable.php 
   */
  public function count() {
    return count($this->sales);
  }
}

/**
 * A Sale.
 */
class Sale extends GiltData {
  
  /**
   * Sale name
   * @var string
   */
  public function getName() {
    return $this->getObj()->name;
  }
  
  /**
   * URL to single sale object
   * e.g. "https://api.gilt.com/v1/sales/women/neutrals-794/detail.json"
   * @var string
   */
  public function getSale() {
    return $this->getObj()->sale;
  }
  
  /**
   * unique identifier for sale
   * @var string
   */
  public function getSaleKey() {
    return $this->getObj()->sale_key;
  }
  
  /**
   * Store key
   * @var string
   */
  public function getStore() {
    return $this->getObj()->store;
  }
  
  /**
   * A description of the sale's theme or brand (optional)
   * @var string
   */
  public function getDescription() {
    if (isset($this->getObj()->description)) {
      return $this->getObj()->description;
    }
    return '';
  }
  
  /**
   * Permalink to sale website
   * @var string
   */
  public function getSaleUrl() {
    return $this->getObj()->sale_url;
  }
  
  /**
   * ISO8601-formatted time for beginning of sale
   * @var string
   */
  public function getBegins() {
    return $this->getObj()->begins;
  }
  
  /**
   * ISO-8601-formatted time for end of sale (optional)
   * @var string
   */
  public function getEnds() {
    return $this->getObj()->ends;
  }
  
  /**
   * See image URLs
   * @var array
   */
  public function getImageUrls() {
    $data = $this->getObj()->image_urls;
    $image_urls = array();
    foreach ($data as $key => $value) {
      $image_urls[$key] = new ImageUrl($value);
    }
    return $image_urls;
  }
  
  /**
   * List of URLs to individual product objects (optional, active sales only)
   * @var array
   */
  public function getProducts() {
    if (isset($this->getObj()->products)) {
      return $this->getObj()->products;
    }
    return array();
  }
  
}

/**
 * A Product.
 */
class Product extends GiltData {

  /**
   * Product name
   * @var string
   */
  public function getName() {
    return $this->getObj()->name;
  }
  
  /**
   * URL to product object
   * @var string
   */
  public function getProduct() {
    return $this->getObj()->product;
  }

  /**
   * Unique identifier for product
   * @var int
   */
  public function getId() {
    return $this->getObj()->id;
  }
  
  /**
   * Brand name
   * @var string
   */
  public function getBrand() {
    return $this->getObj()->brand;
  }
  
  /**
   * Link to product detail page where item can be purchased
   * @var string
   */
  public function getUrl() {
    return $this->getObj()->url;
  }
  
  /**
   * See Image URLs
   * @var array
   */
  public function getImageUrls() {
    $data = $this->getObj()->image_urls;
    $image_urls = array();
    foreach ($data as $key => $value) {
      $image_urls[$key] = new ImageUrl($value);
    }
    return $image_urls;
  }
  
  /**
   * See SKUs
   * @var array
   */
  public function getSkus() {
    $data = $this->getObj()->skus;
    $skus = array();
    foreach ($data as $value) {
      $skus[] = new Sku($value);
    }
    return $skus;
  }
  
  /**
   * An array containing following fields: description, fit_notes, material, care_instructions, origin
   * @var array
   */
  public function getContent() {
    return $this->getObj()->content;
  }
  
  /**
   * Product description (optional)
   * @var string
   */
  public function getDescription() {    
    return $this->getObj()->content->description;
  }

  /**
   * Sizing information (optional)
   * @var string
   */
  public function getFitNotes() {    
    return $this->getObj()->content->fit_notes;
  }

  /**
   * Materials list (optional)
   * @var string
   */
  public function getMaterial() {    
    return $this->getObj()->content->material;
  }

  /**
   * Additional care information (optional)
   * @var string
   */
  public function getCareInstructions() {    
    return $this->getObj()->content->care_instructions;
  }

  /**
   * Place of manufacture (optional)
   * @var string
   */
  public function getOrigin() {    
    return $this->getObj()->content->origin;
  }

}

class ImageUrl extends GiltData {

  /**
   * The URL to the image
   * @var string
   */
  public function getUrl() {    
    return $this->getObj()->url;
  }
  
  /**
   * The width of the image
   * @var int
   */
  public function getWidth() {    
    return $this->getObj()->width;
  }
  
  /**
   * The height of the image
   * @var int
   */
  public function getHeight() {    
    return $this->getObj()->height;
  }

}

class Sku extends GiltData {
  
  /**
   * SKU id
   * @var int
   */
  public function getId() {    
    return $this->getObj()->id;
  }

  /**
   * Describes product availability. One of: "sold out", "for sale", "reserved"
   * @var string
   */
  public function getInventoryStatus() {
    return $this->getObj()->inventory_status;
  }

  /**
   * MSRP price of SKU in US Dollars
   * @var string
   */
  public function getMsrpPrice() {
    return $this->getObj()->msrp_price;
  }

  /**
   * Sale price of SKU in US Dollars
   * @var string
   */
  public function getSalePrice() {
    return $this->getObj()->sale_price;
  }
  
  /**
   * If absent, standard Gilt.com shipping policy and any resulting charges apply. 
   * If present, standard shipping charge is overridden by amount listed here in US Dollars.
   * @var string
   */
  public function getShippingSurcharge() {
    return $this->getObj()->shipping_surcharge;
  }

  /**
   * Name/value pairs of SKU attributes like "color" and "size"
   * @var array
   */
  public function getAttributes() {
    return $this->getObj()->attributes;
  }

}
