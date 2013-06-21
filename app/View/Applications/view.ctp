<?php

$this->extend('/Common/standard');

$this->assign('title',$application['Application']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Applications/_activity_log');
$this->end();
?>

<!-- Main content -->
<section>
<h2 class="float-left">Application Details</h2>
<h2 class="float-right">
  <?php
    echo $this->element('../Applications/_action_menu',array(
      'applicationId' => $application['Application']['id'],
      'actionsDisabled' => !$isAdmin,
      'reload' => true
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
    $formationsTableData = array();
    foreach($formations as $formation){
        $name = $formation['Formation']['name'];
        $id = $formation['Formation']['id'];
        $formationsTableData[] = array(
            $this->Strings->link($name,'/Formations/view/' . $id)
        );
    }
    echo $this->element('Tables/default',array(
      'columnHeadings' => array('Formation'),
      'data' => $formationsTableData
    ));
  ?>
</section>

<section>
<h2>User Privileges</h2>
  <?php
  $permissionsTableData = array();
  foreach($permissions as $permission){

    $sudoRoles = array();
    foreach($permission['SudoRole'] as $sudoRole){
      $name = $sudoRole['SudoRole']['name'];
      $id = $sudoRole['SudoRole']['id'];
      $sudoRoles[] = $this->Strings->link($name,'/SudoRoles/view/' . $id);
    }

    $name = $permission['Team']['name'];
    $id = $permission['Team']['id'];
    $team = $this->Strings->link($name,'/Teams/view/' . $id);

    $permissionsTableData[] = array($team,implode(',',$sudoRoles));
  }
  echo $this->element('Tables/default',array(
    'columnHeadings' => array('Teams','Sudo Roles'),
    'data' => $permissionsTableData
  ));
?>
</section>
