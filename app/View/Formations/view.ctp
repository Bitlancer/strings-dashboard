<?php

$this->extend('/Common/standard');

$this->assign('title',$formation['Formation']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Formations/_activity_log');
$this->end();
?>

<!-- Main content -->
<section>
<h2 class="float-left">Formation Details</h2>
<h2 class="float-right">
  <?php
    echo $this->element('../Formations/_action_menu',array(
      'formationId' => $formation['Formation']['id'],
      'actionsDisabled' => (!$isAdmin || $formation['Formation']['status'] != 'active')
    ));
  ?>
</h2>
<hr class="clear" />
<div id="formation-details">
  <?php
  echo $this->element('Tables/info',array(
    'info' => array(
      'Name' => $formation['Formation']['name'],
      'Status' => $formation['Formation']['status'],
      'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$formation['Formation']['created'])
     )
  ));
  ?>
</div> <!-- /formation-details -->
</section>
