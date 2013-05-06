<!doctype html>
<html lang="en-US">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title> <?php echo $title_for_layout; ?> - Strings | Bitlancer</title>
  <link id="style" rel="stylesheet" type="text/css" href="/css/app.css">
  <?php
		echo $this->Html->meta('icon');

		echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
  ?>
</head>
<body class="loading">
  <header><?php echo $this->element('Layouts/header'); ?></header>
  <nav><?php echo $this->element('Layouts/nav'); ?></nav>
  <section> <!-- Content -->
    <?php echo $this->fetch('content'); ?>
    <?php echo $this->element('sql_dump'); ?>
  </section> <!-- /Content -->
<!-- Javascript -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
<script src="/js/jquery.fcbkcomplete.js"></script>
<script type="text/javascript" src="/js/app.js"></script>
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</body>
</html>
