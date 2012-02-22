<?php
if (isset($this->data['image_url'])) {
?>
  <div><img src="<?php echo $this->data['image_url']; ?>"/></div>
<?php
}
?>
<h2><?php echo $this->data['heading']; ?></h2>
<p><?php echo $this->data['detail']; ?></p>
<?php
if (isset($this->data['product'])) {
  $url = $this->data['product']->getUrl();
?>
  <p><a href="<?php echo $url; ?>" class="btn btn-primary btn-large">Buy on Gilt.com</a></p>
<?php
} 

