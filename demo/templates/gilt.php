<?php
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <base href="<?php echo $this->data['base_url']; ?>"/>
    <meta charset="utf-8">
    <title>Gilt API demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="css/gilt_view.css" rel="stylesheet">
    <style type="text/css">
    </style>
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">

          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Gilt API demo</a>
          <div class="nav-collapse">
            <ul class="nav">

              <li class="active"><a href="http://github.nopolabs.com/gilt_api_php">GitHub</a></li>
              <li><a href="http://nopolabs.com">NopoLabs</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>

    </div>

    <div class="container">

      <div class="hero-unit">
        <?php echo $this->data['hero'] ?>
      </div>

      <div class="row">
        <?php echo $this->data['content']; ?>
      </div>

      <hr>

      <footer>
        <p>&copy; <a href="http://nopolabs.com">NopoLabs</a> 2012</p>
      </footer>

    </div> <!-- /container -->

    <script src="js/jquery-1.7.1.js"></script>
    <script src="js/jquery.masonry.js"></script>
    <script src="bootstrap/js/bootstrap.js"></script>
    <script src="js/gilt_view.js"></script>

  </body>
</html>

