<?php

$this->extend('/Common/standard');

$this->assign('title',$application['Application']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Applications/elements/activity_log');
$this->end();
?>

<!-- Main content -->
<section>
<h2 class="float-left">Application Details</h2>
<h2 class="float-right">
  <?php
    echo $this->element('../Applications/elements/action_menu',array(
      'applicationId' => $application['Application']['id'],
      'actionsDisabled' => !$isAdmin,
      'reloadOnClose' => true
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
<h2>Team Privileges</h2>
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
    'columnHeadings' => array('Team','Sudo Role'),
    'data' => $permissionsTableData
  ));
?>
</section>

<section>
  <?php echo $this->element('Datatables/default',array(
    'model' => 'deviceDns',
    'title' => 'DNS',
    'columnHeadings' => array('Device','DNS Record'),
    'noCta' => true,
    'ctaButtonText' => 'Manage DNS records',
    'ctaTitle' => 'Manage DNS records',
    'dataSrc' => '/Applications/dns/' . $application['Application']['id'],
    'ctaSrc' => '/Applications/manageDnsRecords/' . $application['Application']['id'],
    'ctaModal' => true,
    'pageLength' => 5,
  ));
  ?>
</section>

<section>
  <?php echo $this->element('Datatables/default',array(
    'model' => 'script',
    'title' => 'Deploy Scripts',
    'columnHeadings' => array('Script'),
    'ctaButtonText' => 'Add script',
    'ctaTitle' => 'Add deploy script',
    'dataSrc' => '/Scripts/index/Application/' . $application['Application']['id'],
    'ctaSrc' => '/Scripts/create/Application/' . $application['Application']['id'],
    'ctaModal' => true,
    'ctaWidth' => '500',
    'pageLength' => 5,
  ));
  ?>
</section>
