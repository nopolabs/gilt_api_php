<?php
$partial = new Slim_View();
$partial->setTemplatesDirectory('partials');

function renderPartial($template, $data) {
  global $partial;
  $partial->setData($data);
  return $partial->render($template);
}

