<?php
require 'http_get.php';

class CacheHttpGet extends HttpGet {

  private $cache_dir;

  function __construct($cache_dir) {
    $this->cache_dir = $cache_dir;
    if ( ! file_exists($cache_dir) ) {
      mkdir($cache_dir, 0755);
    }
  }

  public function get($url) {
    $cached = $this->cache_get($url);
    if (isset($cached)) {
      return $cached;
    }

    $content = parent::get($url);

    $this->cache_put($url, $content);

    return $content;
  }

  public function cache_file($key) {
    list($dir, $file) = $this->cache_dir_and_file($key);
    if (file_exists($dir) && file_exists($file)) {
      return $file;
    }
    return null;
  }

  public function cache_get($key) {
    $file = $this->cache_file($key);
    if ($file === null) {
      return null;
    }
    return file_get_contents($file);
  }

  public function cache_put($key, $content) {
    list($dir, $file) = $this->cache_dir_and_file($key);
    if ( ! file_exists($dir) ) {
      mkdir($dir, 0755);
    }
    file_put_contents($file, $content, LOCK_EX);
  }

  public function cache_dir_and_file($key) {
    $sha1 = sha1($key);
    $dir = $this->cache_dir . '/' . substr($sha1, 0, 2) . "/";
    $file = $dir . $sha1;
    return array($dir, $file);
  }
}
