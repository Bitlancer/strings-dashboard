  <h1><?php echo $this->fetch('title'); ?></h1>

  <div class="columns">
    <div>
	  <?php echo $this->element('messages'); ?>
      <?php echo $this->fetch('content'); ?>
	</div>
    <div>
	  <?php echo $this->fetch('sidebar'); ?> 
    </div>
  <hr class="clear">
  </div> <!-- /.columns -->

