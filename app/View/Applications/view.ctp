<?php

$this->extend('/Common/standard');

$this->assign('title', 'Application - ' . $application['Application']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
    'activityLogUri' => ''
));
$this->end();
?>

<!-- Main content -->
<section>
<h2 class="float-left">Application Details</h2>
<h2 class="float-right">
  <?php
    echo $this->element('../Applications/_action_menu',array(
      'applicationId' => $application['Application']['id'],
      'actionsDisabled' => !$isAdmin
    ));
  ?>
</h2>
<hr class="clear" />
<div id="application-details">
  <?php
  echo $this->element('Tables/info',array(
    'info' => array(
      'Name' => $application['Application']['name'],
      'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$application['Application']['created'])
    )
  ));
  ?>
</div> <!-- /application-details -->
</section>

<section>
<h2>Formations</h2>
  <?php
    echo $this->element('Tables/default',array(
      'columnHeadings' => array('Formation'),
      'data' => $formations
    ));
  ?>
</section>

<section>
<h2>Permissions</h2>
  <?php
  echo $this->element('Tables/default',array(
    'columnHeadings' => array('Teams','Sudo Roles'),
    'data' => $permissions
  ));
?>
</section>
