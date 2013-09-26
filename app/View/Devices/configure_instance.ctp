<?php

$deviceId = $device['Device']['id'];
$deviceName = $device['Device']['name'];
$nothingToConfig = empty($variables);

$this->extend('/Common/standard');

$this->assign('title', $deviceName);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Devices/elements/activity_log');
$this->end();
?>

<section id="configure-device" class="configure-instance">
  <h2>System Configuration</h2>
  <form method="post" action="<?php echo $this->here; ?>">
    <?php
      if($nothingToConfig) { ?>
        <div class="empty"><span>This device does not require any configuration</span></div>
      <?php }
      else {
        echo $this->element('../Devices/elements/configure_instance',array(
          'variables' => $variables,
          'variableErrors' => $errors
        ));
      }
    ?>
    <div class="submit">
      <a class="cta primary <?php echo $nothingToConfig ? 'disabled' : 'submit'; ?>">Save</a>
      <a class="cta" href="/Devices/view/<?php echo $deviceId; ?>">Cancel</a>
    </div>
  </form>
</section>
