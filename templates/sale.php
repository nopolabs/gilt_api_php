<?php 
require 'lib/json_format.php';
?>
<html>
<head>
</head>
<body>
<pre>
<?php 
  echo json_format($this->data['sale']->getJson());
?>
</pre>
</body>
</html>

