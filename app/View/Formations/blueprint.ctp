<?php

$this->extend('/Common/standard');

$this->assign('title', 'Create Formation');

//Set sidebar content
$this->start('sidebar');
$this->end();
?>

<!-- Main content -->
<section>
<h2>1. Select a Blueprint</h2>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="blueprint" value="" />
<?php
    echo $this->element('Datatables/nocta',array( 
      'model' => 'blueprint',
      'title' => 'Blueprints',
      'columnHeadings' => array('Blueprint','Description'),
      'dataSrc' => $_SERVER['REQUEST_URI'] . ".json"
  ));
?>
</form>
</section>
