<?php 
$store_key = $this->data['store_key'];
$store = $this->data['store'];
foreach ($store as $sale) {
  $url = substr($sale->getSale(), strlen(Gilt::BASE_URL_V1));
  $url = preg_replace('#/\w*\.json#', '', $url);
  $imageUrls = $sale->getImageUrls();
  $imgUrl = $imageUrls['100x93']->getUrl();
?>
  <div class="item">
    <h2><a href="<?php echo $url; ?>"><?php echo $sale->getName(); ?></a></h2>
    <p><?php echo $sale->getDescription(); ?></p>
    <img src="<?php echo $imgUrl; ?>"/>
  </div>
<?php
}

