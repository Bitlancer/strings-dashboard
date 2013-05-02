<ul id="notice">
  <?php
  echo $this->Session->flash('flash', array('element' => 'Notices/default'));
  echo $this->Session->flash('error', array('element' => 'Notices/error'));
  echo $this->Session->flash('success', array('element' => 'Notices/success'));
  echo $this->Session->flash('auth', array('element' => 'Notices/error'));
  ?>
</ul>
