<?php

$this->extend('/Common/standard');

$this->assign('title', 'User');

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
    'activityLogUri' => ''
));
$this->end();
?>

<!-- Main content -->
<section>
  <h2 class="float-left">User Details</h2>
  <h2 class="float-right">
  <?php 
    echo $this->element('../Users/_action_menu',array(
        'align' => 'right',
        'userId' => $user['User']['id'],
        'userEnabled' => !$user['User']['is_disabled'],
        'actionsDisabled' => !$isAdmin
    ));  
  ?>
  </h2>
  <hr class="clear" />
  <div id="user-details">
  <?php 
    echo $this->StringsTable->infoTable(array(
		'Status' => $user['User']['is_disabled'] ? 'Disabled' : 'Enabled',
		'Username' => $user['User']['name'],
		'Name' => $user['User']['full_name'],
		'Email' => $user['User']['email'],
		'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$user['User']['created'])
  ));
  ?>
  </div>
</section>
