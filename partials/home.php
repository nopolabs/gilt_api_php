<?php 
$stores = $this->data['stores'];
foreach ($stores as $store_key => $store) {
?>
<div class="item">
  <h2><?php echo $store_key; ?></h2>
<?php
  $count = 0;
  foreach ($store as $sale) {
    $count = $count + 1;
    if ($count > 5) {
      $remaining = count($store) - $count;
      if ($remaining > 0) {
?>
    and <a href="<?php echo 'sales/' . $store_key; ?>"><?php echo $remaining . ' more sale' . (($remaining > 0) ? 's' : ''); ?></a>
<?php
      }
      break;
    }
    $url = substr($sale->getSale(), strlen(Gilt::BASE_URL_V1));
    $url = preg_replace('#/\w*\.json#', '', $url);
?>
    <a href="<?php echo $url; ?>"><?php echo $sale->getName(); ?></a><br/>
<?php
  }
?>
</div>
<?php
}
