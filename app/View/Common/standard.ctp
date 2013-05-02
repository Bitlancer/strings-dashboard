  <h1><?php echo $this->fetch('title'); ?></h1>

  <?php
    echo $this->Session->flash('error');
    echo $this->Session->flash('success');
  ?>

  <div class="columns">
    <div>
      <?php echo $this->fetch('content'); ?>
	</div>
    <div>
	  <?php echo $this->fetch('sidebar'); ?> 
    </div>
  <hr class="clear">
  </div> <!-- /.columns -->

