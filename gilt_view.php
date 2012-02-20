<?php
class GiltView extends Slim_View {

  private $innerView;
  private $outerView;

  public function __construct() {
    $this->innerView = new Slim_View();
    $this->outerView = new Slim_View();
  }

  public function setTemplatesDirectory($dir) {
    $tdir = rtrim($dir, '/');
    $this->innerView->setTemplatesDirectory($tdir);
    $this->outerView->setTemplatesDirectory($tdir);
  }

  public function render($template) {
    $this->innerView->setData($this->data);
    $content = $this->innerView->render($template);
    if ($template == 'json.php') {
      return $content;
    }
    $this->outerView->setData(array('content' => $content));
    return $this->outerView->render('gilt.php');
  }
}
