<?php

$deviceId = $device['Device']['id'];
$deviceName = $device['Device']['name'];

$this->extend('/Common/standard');

$this->assign('title', $deviceName);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Devices/elements/activity_log');
$this->end();
?>

<section id="configure-device" class="configure-load-balancer">
  <h2>Load-balancer Configuration</h2>
  <?php echo $this->Form->create('Device',array(
    'url' => $this->here,
    'class' => 'vertical-labels'
  ));
  ?>
  <?php if(!empty($errors)) {
    echo $this->element('notice-list',array(
      'type' => 'error',
      'errors' => $errors
    ));
  } ?>
  <?php
    echo $this->element('../Devices/elements/configure_loadbalancer',array(
      'virtualIpTypes' => $loadBalancerVirtualIpTypes,
      'protocols' => $loadBalancerProtocols,
      'protocolPortMap' => $loadBalancerProtocolPortMap,
      'algorithms' => $loadBalancerAlgorithms
    ));
  ?>
  <div class="submit">
    <a class="cta primary submit">Save</a>
    <a class="cta" href="/Devices/view/<?php echo $deviceId; ?>">Cancel</a>
  </div>
  <?php echo $this->Form->end(); ?>
</section>
