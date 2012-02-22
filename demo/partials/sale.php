<?php
$products = $this->data['products'];
foreach ($products as $product) {
  $url = 'product/' . $product->getId();
  $imageUrls = $product->getImageUrls();
  $imgUrl = $imageUrls['91x121']->getUrl();
?>
  <div class="item">
    <h2><a href="<?php echo $url; ?>"><?php echo $product->getName(); ?></a></h2>
    <p><?php echo $product->getDescription(); ?></p>
    <img src="<?php echo $imgUrl; ?>"/>
  </div>
<?php
}
