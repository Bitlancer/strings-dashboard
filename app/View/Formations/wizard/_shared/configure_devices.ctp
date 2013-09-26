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
    $deviceTypeId = $device['deviceTypeId'];
    $roleId = $device['roleId'];
    $blueprintPartName = $device['blueprintPartName'];
    $inErrorState = isset($devicesInErrorState) && in_array($id,$devicesInErrorState);
    $deviceErrors = $inErrorState ? $errors[$id] : array();
    $hasGeneralError = isset($deviceErrors['general']);
    $hasInfraError = isset($deviceErrors['infrastructure']);
    ?>
    <div class="tab <?php echo $inErrorState ? 'error' : ''; ?>" data-tab="device-<?php echo $id; ?>">
      <?php if($hasGeneralError) {
        echo $this-elements('notice-list',array(
          'type' => 'error',
          'errors' => $deviceErrors['general']
        ));
      } ?> 
      <fieldset class="infrastructure-configuration">
        <legend>Infrastructure Configuration</legend>
        <?php if($hasInfraError) {
          echo $this-elements('notice-list',array(
            'type' => 'error',
            'errors' => $deviceErrors['infrastructure']
          ));
        } ?>
        <?php
        if($deviceTypeId == 1){ //Flavor is only applicable for instances
          echo $this->Form->input("Device.$id.flavor",array(
            'label' => 'Flavor',
            'error' => false,
            'options' => $instanceFlavors
          ));
        }
        echo $this->Form->input("Device.$id.region",array(
          'label' => 'Target',
          'error' => false,
          'options' => $regions
        ));
        ?>
      </fieldset> <!-- /.infrastructure-configuration -->
      <?php
        if($deviceTypeId == 1){
            $varDefs = isset($instanceVarDefs[$roleId]) ?
                $instanceVarDefs[$roleId] : array();
            $variableErrors = isset($deviceErrors['system']) ?
                $deviceErrors['system'] : array();
          ?>
          <fieldset class="system-configuration">
            <legend>System Configuration</legend>
            <?php if(empty($varDefs)) { ?>
              <div class="empty">
                <span>This device does not require any configuration</span>
              </div>
            <?php }
            else {
              echo $this->element('../Devices/elements/configure_instance',array(
                'inputPrefix' => "Device.$id",
                'variableDefs' => $varDefs,
                'variableErrors' => $variableErrors,
              )); 
            } ?>
          </fieldset>
        <?php
        }
        elseif($deviceTypeId == 2){ ?>
          <fieldset class="load-balancer-configuration">
            <legend>Load-balancer Configuration</legend>
            <?php if(isset($deviceErrors['load-balancer'])) {
              echo $this->element('notice-list',array(
                'type' => 'error',
                'errors' => $deviceErrors['load-balancer']
              ));
            } ?> 
            <?php
              echo $this->element('../Devices/elements/configure_loadbalancer',array(
                'inputPrefix' => "Device.$id",
                'virtualIpTypes' => $loadBalancerVirtualIpTypes,
                'protocols' => $loadBalancerProtocols,
                'protocolPortMap' => $loadBalancerProtocolPortMap,
                'algorithms' => $loadBalancerAlgorithms
              ));
            ?>
          </fieldset>
        <?php }
        else {
            throw new InteralErrorException('Unexpected device type.');
        }
      ?>
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
