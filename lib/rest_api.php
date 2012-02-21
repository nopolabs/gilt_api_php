<?php

require_once 'rest_curl_client.php';

class RestApi {
  
  private $log_file;

  public function __construct($log_file = null) {
    $this->log_file = $log_file;
  }

  protected function log($msg) {
    if (isset($this->log_file)) {
      file_put_contents($this->log_file, $msg."\n", FILE_APPEND);
    }
  }

  public function getUrl($url){
    $rest = new RestCurlClient();
    $rsp = $rest->get($url);
    $this->log('{"request":"'.$url.'","response":'.$rsp.'}');
    return $rsp;
  }
}
