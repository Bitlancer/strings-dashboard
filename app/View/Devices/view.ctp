<?php

$this->extend('/Common/standard');

$this->assign('title', 'Device - ' . $device['Device']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
    'activityLogUri' => ''
));
$this->end();
?>

<!-- Main content -->
<section>
<h2 class="float-left">Device Details</h2>
<h2 class="float-right">
  <?php
    /*
    echo $this->element('../Device/_action_menu',array(
      'deviceId' => $team['Team']['id'],
      'actionsDisabled' => !$isAdmin
    ));
    */
  ?>
</h2>
<hr class="clear" />
<div id="device-details">
  <?php
  echo $this->StringsTable->infoTable(array(
    'Name' => $device['Device']['name'],
    'Role' => $device['Role']['name'],
    'Parent Formation' => $device['Formation']['name'],
    'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$device['Device']['created'])
  ));
  ?>
</div> <!-- /device-details -->
</section>

<section>
<h2>Provider Details</h2>
  <?php
  echo $this->StringsTable->infoTable(array(
    'Provider' => $providerDetails['provider_name'],
    'Region' => $providerDetails['region'],
    'Image' => $providerDetails['image'],
    'Flavor' => $providerDetails['flavor'], 
  ));
  ?>
</section>

<section>
<h2>Interfaces</h2>
  <?php
  foreach($deviceInterfaces as $index => $row){
    $row[1] = "<a href=\"ssh://\"" . $row[1] . "\">" . $row[1] . "</a>";
    $deviceInterfaces[$index] = $row;
  }
  echo $this->StringsTable->table(array('Network','Address'),$deviceInterfaces);
  ?>
</section>


<?php print_r($device); ?>
