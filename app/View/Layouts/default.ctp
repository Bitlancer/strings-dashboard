<!doctype html>
<html lang="en-US">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="refresh" content="<?php echo Configure::read('Session.timeout') * 60 + 15; ?>" />
  <title> <?php echo $title_for_layout; ?> - Strings | Bitlancer</title>
  <link rel="stylesheet" type="text/css" href="/css/app.css">
  <link rel="stylesheet" type="text/css" href="/css/jquery.ui.theme.css">
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
  <?php
		echo $this->Html->meta('icon');
		echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
  ?>
</head>
<body class="loading">
  <header><?php echo $this->element('../Layouts/Elements/header'); ?></header>
  <nav><?php echo $this->element('../Layouts/Elements/nav'); ?></nav>
  <section> <!-- Content -->
    <?php echo $this->fetch('content'); ?>
  </section> <!-- /Content -->
<!-- Javascript -->
<script type="text/javascript" src="/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/js/jquery.dataTables.plugins.js"></script>
<script type="text/javascript" src="/js/jquery.fcbkcomplete.js"></script>
<script type="text/javascript" src="/js/app.js"></script>
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</body>
</html>
