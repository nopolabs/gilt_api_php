<?php
if (isset($this->data['image_url'])) {
?>
  <div><img src="<?php echo $this->data['image_url']; ?>"/></div>
<?php
}
?>
<h2><?php echo $this->data['heading']; ?></h2>
<p><?php echo $this->data['detail']; ?></p>
