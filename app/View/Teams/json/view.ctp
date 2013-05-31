<div id="view-team">
  <h2>
    <?php 
    echo $this->element('../Teams/_action_menu',array(
      'align' => 'left',
      'teamId' => $team['Team']['id'],
      'teamEnabled' => !$team['Team']['is_disabled'],
      'actionsDisabled' => !$isAdmin
    ));
    ?>
  </h2>
  <div style="margin-bottom:15px;">
    <?php 
    echo $this->StringsTable->infoTable(array(
      'Status' => $team['Team']['is_disabled'] ? 'Disabled' : 'Enabled',
      'Name' => $team['Team']['name'],
      'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$team['Team']['created'])
    ));
    ?>
  </div>
  <div>
    <?php
    $memberTableData = array();
    foreach($members as $member)
      $memberTableData[][] = $member['User']['full_name'] . " (" . $member['User']['name'] . ")";
    echo $this->StringsTable->table(array('Members'),$memberTableData);
    ?>
  </div>
</div> <!-- /view-user -->
