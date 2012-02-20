<h1><?php echo $this->data['heading']; ?></h1>
<p><?php echo $this->data['detail']; ?></p>
<?php
if (isset($this->data['image_url'])) {
  echo '<img src="' . $this->data['image_url'] . '"/>';
}
