<?php

require_once 'rest_curl_client.php';

class RestApi {
  
  private $base_url;
  private $base_params;
  private $log_file;

  public function __construct($base_url, $base_params, $log_file = null) {
    $this->base_url = $base_url;
    $this->base_params = $base_params;
    $this->log_file = $log_file;
  }

  protected function log($msg) {
    if (isset($this->log_file)) {
      file_put_contents($this->log_file, $msg."\n", FILE_APPEND);
    }
  }

  public function getPath($path_els = array(), $params = array()) {
    $url = $this->buildUrl($path_els);
    return $this->getUrl($url, $params);
  }

  public function getUrl($url, $params = array()){
    $url = $this->appendParams($url, $params);
    $rest = new RestCurlClient();
    $rsp = $rest->get($url);
    $this->log('{"request":"'.$url.'","response":'.$rsp.'}');
    return $rsp;
  }

  function buildUrl($path_els, $params = array()) {
    $url = '';
    foreach ($path_els as $el) {
      if (empty($el)) continue;
      $url = (empty($url)) ? '' : ($url . '/');
      $url = $url . $el;
    }
    return $this->base_url . $url;
  }

  function appendParams($url, $params = array()) {
    $params = array_merge($this->base_params, $params);
    if (strstr($url, '?')) {
      list($url, $query) = explode('?', $url, 2);
    } else {
      $query = '';
    }
    foreach ($params as $key => $value) {
      $query = (empty($query)) ? '?' : ($query . '&');
      $query = $query . $key . '=' . urlencode($value);
    }
    $url = $url . $query;
    return $url;
  }
}
