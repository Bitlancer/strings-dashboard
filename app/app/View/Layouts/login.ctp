<!doctype html>
<html lang="en-US">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Strings - Sign In | Bitlancer</title>
  <link id="style" rel="stylesheet" type="text/css" href="/css/app.css">
   <?php
		echo $this->Html->meta('icon');

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
  ?>
</head>
<body class="loading" id="account">
  <header></header>
  <?php echo $this->fetch('content'); ?>
<!-- Javascript -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script type="text/javascript" src="/js/login.js"></script>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<div id="preload">
  <img src="/img/wall3.jpg" />
</div>
</body>
</html>
