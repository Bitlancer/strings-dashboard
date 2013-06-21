<?php

$this->extend('/Common/standard');

$this->assign('title',$sudoRole['SudoRole']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../SudoRoles/_activity_log');
$this->end();
?>

<!-- Main content -->
<section>
<h2 class="float-left">Sudo Role Details</h2>
<h2 class="float-right">
  <?php
    echo $this->element('../SudoRoles/_action_menu',array(
      'sudoRoleId' => $sudoRole['SudoRole']['id'],
      'actionsDisabled' => !$isAdmin
    ));
  ?>
</h2>
<hr class="clear" />
<div id="sudo-role-details">
  <?php
  echo $this->element('Tables/info',array(
    'info' => array(
      'Name' => $sudoRole['SudoRole']['name'],
      'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$sudoRole['SudoRole']['created'])
    )
  ));
  ?>
</div> <!-- /team-details -->
</section>

<section>
<h2>Run as</h2>
<div>
  <?php
    $runasTableData = array();
    foreach($runas as $r)
      $runasTableData[][] = $r;
    echo $this->element('Tables/default',array(
      'columnHeadings' => array('User'),
      'data' => $runasTableData
    ));
  ?>
</div>
</section>

<section>
<h2>Commands</h2>
<div>
  <?php
    $commandsTableData = array();
    foreach($commands as $c)
      $commandsTableData[][] = $c;
    echo $this->element('Tables/default',array(
      'columnHeadings' => array('Command'),
      'data' => $commandsTableData
    ));
  ?>
</div>
</section>
