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

<section id="configure-device">
  <h2>System Configuration</h2>
  <form method="post" action="<?php echo $this->here; ?>">
    <?php echo $this->element('../Devices/elements/configure',array(
      'modulesAndVariables' => $modulesVariables,
      'variableErrors' => $errors
    )); ?>
  <div class="submit">
    <a class="cta primary submit">Save</a>
    <a class="cta" href="/Devices/view/<?php echo $deviceId; ?>">Cancel</a>
  </div>
  </form>
</section>
