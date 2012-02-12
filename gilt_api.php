<?php
/**
 * Gilt API PHP
 *
 *  $api_key = <your_gilt_api_key>;
 *  $gilt = new Gilt($api_key);
 *  $sales = $gilt->getActiveSales();
 */
 
require_once __DIR__.'/lib/rest_api.php';
 
class Gilt {

  /**
   * The version of this lib
   */
  const VERSION = '0.1.0';

  const WOMEN = 'women';
  const MEN   = 'men';
  const KIDS  = 'kids';
  const HOME  = 'home';

  private $rest_api;
  
  public function __construct($api_key, $rest_api = null) {
    if (empty($rest_api)) {
      $this->rest_api = new RestApi('https://api.gilt.com/v1/', array('apikey'=>$api_key));
    } else {
      $this->rest_api = $rest_api;
    }
  }

  private function _getGiltData($path_els = array()) {
    $json = $this->rest_api->get($path_els);
    return GiltData::fromJson($json);
  }
    
  private function _getSales($path_els = array()) {
    array_unshift($path_els, 'sales');
    $gilt_data = $this->_getGiltData($path_els);
    return new Sales($gilt_data);
  }
  
  private function _getSale($path_els = array()) {
    array_unshift($path_els, 'sales');
    $gilt_data = $this->_getGiltData($path_els);
    return new Sale($gilt_data);
  }
  
  private function _getProduct($path_els = array()) {
    array_unshift($path_els, 'products');
    $gilt_data = $this->_getGiltData($path_els);
    return new Product($gilt_data);
  }
  
  public function getActiveSales($store_key = null) {
    return $this->_getSales(array($store_key, 'active.json'));
  }
  
  public function getUpcomingSales($store_key = null) {
    return $this->_getSales(array($store_key, 'upcoming.json'));
  }
  
  public function getSale($store_key, $sale_key) {
    return $this->_getSale(array($store_key, $sale_key, 'detail.json'));
  }
  
  public function getProduct($product_id) {
    return $this->_getProduct(array($product_id, 'detail.json'));
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
      $this->json = json_encode($this->data);
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
  
}

class Sales extends GiltDataHolder {

  private $sales;
  
  public function getSales() {
    if (!isset($this->sales)) {
      $this->sales = array();
      $data = $this->getData();
      foreach ($data->sales as $sale_data) {
        $gilt_data = GiltData::fromData($sale_data);
        $sale = new Sale($gilt_data);
        array_push($this->sales, $sale);
      }
    }
    return $this->sales;
  }
}

class Sale extends GiltDataHolder {
  
  /**
   * Sale name
   * @var string
   */
  public function getName() {
    return $this->getData()->name;
  }
  
  /**
   * URL to single sale object
   * @var string
   */
  public function getSale() {
    return $this->getData()->sale;
  }
  
  /**
   * unique identifier for sale
   * @var string
   */
  public function getSaleKey() {
    return $this->getData()->sale_key;
  }
  
  /**
   * Store key
   * @var string
   */
  public function getStore() {
    return $this->getData()->store;
  }
  
  /**
   * A description of the sale's theme or brand (optional)
   * @var string
   */
  public function getDescription() {
    return $this->getData()->description;
  }
  
  /**
   * Permalink to sale website
   * @var string
   */
  public function getSaleUrl() {
    return $this->getData()->sale_url;
  }
  
  /**
   * ISO8601-formatted time for beginning of sale
   * @var string
   */
  public function getBegins() {
    return $this->getData()->begins;
  }
  
  /**
   * ISO-8601-formatted time for end of sale (optional)
   * @var string
   */
  public function getEnds() {
    return $this->getData()->ends;
  }
  
  /**
   * See image URLs
   * @var array
   */
  public function getImageUrls() {
    $data = $this->getData()->image_urls;
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
  public function getProducts() {
    return $this->getData()->products;
  }
  
}

class Product extends GiltDataHolder {

  /**
   * Product name
   * @var string
   */
  public function getName() {
    return $this->getData()->name;
  }
  
  /**
   * URL to product object
   * @var string
   */
  public function getProduct() {
    return $this->getData()->product;
  }
  
  /**
   * Unique identifier for product
   * @var int
   */
  public function getId() {
    return $this->getData()->id;
  }
  
  /**
   * Brand name
   * @var string
   */
  public function getBrand() {
    return $this->getData()->brand;
  }
  
  /**
   * Link to product detail page where item can be purchased
   * @var string
   */
  public function getUrl() {
    return $this->getData()->url;
  }
  
  /**
   * See Image URLs
   * @var array
   */
  public function getImageUrls() {
    return $this->getData()->image_urls;
  }
  
  /**
   * See SKUs
   * @var array
   */
  public function getSkus() {
    $data = $this->getData()->skus;
    $skus = array();
    foreach ($data as $value) {
      $skus[] = new Sku(GiltData::fromData($value));
    }
    return $skus;
  }
  
  /**
   * An array containing following fields: description, fit_notes, material, care_instructions, origin
   * @var array
   */
  public function getContent() {
    return $this->getData()->content;
  }
  
  /**
   * Product description (optional)
   * @var string
   */
  public function getDescription() {    
    return $this->getData()->content->description;
  }

  /**
   * Sizing information (optional)
   * @var string
   */
  public function getFitNotes() {    
    return $this->getData()->content->fit_notes;
  }

  /**
   * Materials list (optional)
   * @var string
   */
  public function getMaterial() {    
    return $this->getData()->content->material;
  }

  /**
   * Additional care information (optional)
   * @var string
   */
  public function getCareInstructions() {    
    return $this->getData()->content->care_instructions;
  }

  /**
   * Place of manufacture (optional)
   * @var string
   */
  public function getOrigin() {    
    return $this->getData()->content->origin;
  }

}

class ImageUrl extends GiltDataHolder {

  /**
   * The URL to the image
   * @var string
   */
  public function getUrl() {    
    return $this->getData()->url;
  }
  
  /**
   * The width of the image
   * @var int
   */
  public function getWidth() {    
    return $this->getData()->width;
  }
  
  /**
   * The height of the image
   * @var int
   */
  public function getHeight() {    
    return $this->getData()->height;
  }

}

class Sku extends GiltDataHolder {
  
  /**
   * SKU id
   * @var int
   */
  public function getId() {    
    return $this->getData()->id;
  }

  /**
   * Describes product availability. One of: "sold out", "for sale", "reserved"
   * @var string
   */
  public function getInventoryStatus() {
    return $this->getData()->inventory_status;
  }

  /**
   * MSRP price of SKU in US Dollars
   * @var string
   */
  public function getMsrpPrice() {
    return $this->getData()->msrp_price;
  }

  /**
   * Sale price of SKU in US Dollars
   * @var string
   */
  public function getSalePrice() {
    return $this->getData()->sale_price;
  }
  
  /**
   * If absent, standard Gilt.com shipping policy and any resulting charges apply. 
   * If present, standard shipping charge is overridden by amount listed here in US Dollars.
   * @var string
   */
  public function getShippingSurcharge() {
    return $this->getData()->shipping_surcharge;
  }

  /**
   * Name/value pairs of SKU attributes like "color" and "size"
   * @var array
   */
  public function getAttributes() {
    return $this->getData()->attributes;
  }

}
