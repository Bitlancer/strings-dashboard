<?php

$this->extend('/Common/standard');

$this->assign('title', 'Team - ' . $team['Team']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
    'activityLogUri' => ''
));
$this->end();
?>

<!-- Main content -->
<section>
<h2 class="float-left">Team Details</h2>
<h2 class="float-right">
  <?php
    echo $this->element('../Teams/_action_menu',array(
      'teamId' => $team['Team']['id'],
      'teamEnabled' => !$team['Team']['is_disabled'],
      'actionsDisabled' => !$isAdmin
    ));
  ?>
</h2>
<hr class="clear" />
<div id="team-details">
  <?php
  echo $this->StringsTable->infoTable(array(
    'Status' => $team['Team']['is_disabled'] ? 'Disabled' : 'Enabled',
    'Name' => $team['Team']['name'],
    'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$team['Team']['created'])
  ));
  ?>
</div> <!-- /team-details -->
</section>

<section>
<h2>Team Members</h2>
<div>
  <?php
    $memberTableData = array();
    foreach($members as $member)
      $memberTableData[][] = $member['User']['full_name'] . " (" . $member['User']['name'] . ")";
    echo $this->StringsTable->table(array('Members'),$memberTableData);
  ?>
</div>
</section>
