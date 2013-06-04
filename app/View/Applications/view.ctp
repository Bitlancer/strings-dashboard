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
  echo $this->StringsTable->infoTable(array(
    'Name' => $application['Application']['name'],
    'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$application['Application']['created'])
  ));
  ?>
</div> <!-- /application-details -->
</section>

<section>
<h2>Formations</h2>
  <?php
  $formationsTableData = array();
  foreach($application['ApplicationFormation'] as $formation){
    $formationsTableData[] = array(
      'displayValue' => $formation['Formation']['name'],
      'id' => $formation['Formation']['id']
    );
  }
  echo $this->element('Associations/form',array(
    'memberData' => array(),
    'emptyTableMessage' => 'Add a formation above',
    'addInputPlaceholder' => 'formation name',
    'addAutocompleteUri' => '/Formations/searchByName',
    'addAssociationUri' => '/Applications/addFormation/' . $application['Application']['id'] . '.json',
    'removeAssociationUri' => '/Applications/removeFormation/' . $application['Application']['id'] . '.json',
  ));
  ?>
</section>

<section>
<h2>Permissions</h2>
<div>
  <?php
    $permissionsTableData = array();
    foreach($application['TeamApplication'] as $team){
      $row = $this->Strings->modalLink($team['Team']['name'],'/Applications/editTeamPermissions/' . $team['Team']['id']);
      $row .= $this->element('../Applications/_team_action_menu',array(
        'applicationId' => $application['Application']['id'],
        'teamId' => $team['Team']['id']
      ));
      $permissionsTableData[][] = $row;
    }
    echo $this->StringsTable->table(array('Teams'),$permissionsTableData);
  ?>
</div>
