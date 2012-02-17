<?php 
require 'lib/json_format.php';
?>
<html>
<head>
</head>
<body>
<pre>
<?php 
  echo json_format($this->data['product']->getJson());
?>
</pre>
</body>
</html>


