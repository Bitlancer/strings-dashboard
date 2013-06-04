<?php

$this->extend('/Common/standard');

$this->assign('title', 'Sudo Role - ' . $sudoRole['SudoRole']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
    'activityLogUri' => ''
));
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
  echo $this->StringsTable->infoTable(array(
    'Name' => $sudoRole['SudoRole']['name'],
    'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$sudoRole['SudoRole']['created'])
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
    echo $this->StringsTable->table(array('User'),$runasTableData);
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
    echo $this->StringsTable->table(array('Command'),$commandsTableData);
  ?>
</div>
</section>
