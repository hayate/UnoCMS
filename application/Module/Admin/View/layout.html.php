<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo ucfirst($_SERVER['SERVER_NAME']) ?></title>
    <!--link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" /-->
    <link rel="stylesheet" href="/css/blueprint/screen.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print" />
    <!--[if lt IE 8]>
        <link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection" />
    <![endif]-->
    <link rel="stylesheet" href="/admin/index/resource/style.css" type="text/css" media="screen" />
    <?php echo $style ?>
    <script type="text/javascript" src="/js/jquery-1.6.1.min.js"></script>
    <?php echo $jscript ?>
  </head>
  <body>
    <?php echo isset($header) ? $header : '' ?>

    <?php echo isset($content) ? $content : '' ?>
  </body>
</html>
