<?php
$imageUrls = $this->data['sale']->getImageUrls();
$url = $imageUrls['636x400']->getUrl();
?>
<img src="<?php echo $url; ?>"/>
<?php
foreach($this->data['sale']->getImageUrls() as $key => $imageUrl) {
  echo '<br/>'.$key.'<br/><code>';
  var_dump($imageUrl);
  echo '</code>';
}
