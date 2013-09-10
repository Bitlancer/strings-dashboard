<?php
$this->extend('/Formations/wizard/create/_template');

$this->assign('stepTitle','Configure Devices');
$this->assign('forwardButtonText','Complete');
?>

<div id="configure-devices" class="vtab">
  <nav>
    <?php foreach($devices as $device){
      $id = $device['psuedoId'];
      $name = $device['name'];
      $partName = $device['blueprintPartName'];
      $inErrorState = isset($devicesInErrorState) && in_array($id,$devicesInErrorState);
      ?>
      <a class="<?php echo $inErrorState ? "error" : ""; ?>" data-tab="device-<?php echo $id; ?>">
        <span class="title"><?php echo $name; ?></span>
        <span class="subtitle"><?php echo $partName; ?></span>
        <span class="status"></span>
      </a>
    <?php } ?>
  </nav>
  <div class="tabs">
  <?php foreach($devices as $device){
    $id = $device['psuedoId'];
    $name = $device['name'];
    $blueprintPartId = $device['blueprintPartId'];
    $blueprintPartName = $device['blueprintPartName'];
    $modulesAndVariables = $blueprintPartModulesAndVariables[$blueprintPartId];
    $inErrorState = isset($devicesInErrorState) && in_array($id,$devicesInErrorState);
    $deviceInfraErrors = $inErrorState && isset($infraConfigErrors[$id]) ? $infraConfigErrors[$id] : array();
    $deviceSystemErrors = $inErrorState && isset($systemConfigErrors[$id]) ? $systemConfigErrors[$id] : array();
    ?>
    <div class="tab <?php echo $inErrorState ? 'error' : ''; ?>" data-tab="device-<?php echo $id; ?>">
      <fieldset class="infrastructure-configuration">
        <legend>Infrastructure Configuration</legend>
        <?php if(count($deviceInfraErrors)) { ?>
          <ul class="notice-list error">
            <?php foreach($deviceInfraErrors as $error) { ?>
              <li><?php echo $error; ?></li>
            <?php } ?>
          </ul>
        <?php } ?>
        <?php
        echo $this->Form->input("Device.$id.flavor",array(
          'label' => 'Flavor',
          'error' => false,
          'options' => $flavors
        ));
        echo $this->Form->input("Device.$id.region",array(
          'label' => 'Target',
          'error' => false,
          'options' => $regions
        ));
        ?>
      </fieldset> <!-- /.infrastructure-configuration -->
      <fieldset class="system-configuration">
        <legend>System Configuration</legend>
        <?php echo $this->element('../Devices/elements/configure',array(
          'modulesAndVariables' => $modulesAndVariables,
          'variableErrors' => $deviceSystemErrors,
          'inputPrefix' => "Device.$id"
        )); ?>
      </fieldset> <!-- /.system-configuration -->
    </div> <!-- /tab -->
  <?php } ?>
  </div> <!-- /.tabs -->
  <div class="clear"></div>
</div><!-- /.configure-devices -->

<?php $this->start('stepScript'); ?>
  $('.cta.primary').removeClass('disabled');
  $('.cta.primary').click('live',function(){
    var form = $(this).closest('form');
    $('.device-modal').css('display','none').appendTo(form);
    form.submit();
  });
<?php $this->end(); ?>
