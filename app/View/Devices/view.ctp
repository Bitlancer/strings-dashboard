<?php

$this->extend('/Common/standard');

$this->assign('title',$device['Device']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Devices/elements/activity_log');
$this->end();
?>

<!-- Main content -->
<section>
<h2 class="float-left">Device Details</h2>
<h2 class="float-right">
  <?php
    $actionMenuElement = $device['DeviceType']['name'] == 'load-balancer' ? 
      '../Devices/elements/loadbalancer_action_menu' :
      '../Devices/elements/instance_action_menu';
    echo $this->element($actionMenuElement,array(
        'deviceId' => $device['Device']['id'],
        'formationId' => $device['Device']['formation_id'],
        'actionsDisabled' => (!$isAdmin || $device['Device']['status'] !== 'active')
    ));
  ?>
</h2>
<hr class="clear" />
<div id="device-details">
  <?php
  echo $this->element('Tables/info',array(
    'info' => array(
      'Name' => $device['Device']['name'],
      'Status' => $device['Device']['status'],
      'Role' => $device['Role']['name'],
      'Parent Formation' => $device['Formation']['name'],
      'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$device['Device']['created'])
    )
  ));
  ?>
</div> <!-- /device-details -->
</section>

<section>
<h2>Provider Details</h2>
  <?php
  echo $this->element('Tables/info',array(
    'info' => $providerInfo
  ));
  ?>
</section>

<section>
<h2>Addresses</h2>
  <?php
  foreach($deviceAddresses as $index => $row){
    $row[1] = "<a href=\"ssh://" . $row[1] . "\">" . $row[1] . "</a>";
    $deviceAddresses[$index] = $row;
  }
  echo $this->element('Tables/default',array(
    'columnHeadings' => array('Network','Address'),
    'data' => $deviceAddresses
  ));
  ?>
</section>

