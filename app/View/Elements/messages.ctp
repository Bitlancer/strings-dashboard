<ul id="messages">
  <?php echo $this->Session->flash('flash', array('element' => 'Messages/default')); ?>
  <?php echo $this->Session->flash('error', array('element' => 'Messages/error')); ?>
  <?php echo $this->Session->flash('warning', array('element' => 'Messages/warning')) ?>
  <?php echo $this->Session->flash('success', array('element' => 'Messages/success')); ?>
</ul>
