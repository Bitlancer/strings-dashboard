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
      $inErrorState = isset($errors[$id]);
      ?>
      <a class="<?php echo $inErrorState ? "error" : ""; ?>" data-tab="device-<?php echo $id; ?>">
        <span class="name"><?php echo $name; ?></span>
        <small><?php echo $partName; ?></small>
        <span class="status"></span>
      </a>
    <?php } ?>
  </nav>
  <div class="tabs">
  <?php foreach($devices as $device){
    $psuedoId = $device['psuedoId'];
    $name = $device['name'];
    $blueprintPartId = $device['blueprintPartId'];
    $blueprintPartName = $device['blueprintPartName'];
    $modulesAndVariables = $blueprintPartModulesAndVariables[$blueprintPartId];
    $inErrorState = isset($errors[$psuedoId]);
    ?>
    <div class="tab <?php echo $inErrorState ? 'error' : ''; ?>" data-tab="device-<?php echo $psuedoId; ?>">
      <div class="provider">
        <h2>Provider</h2>
        <?php
        echo $this->Form->input("Device.$psuedoId.flavor",array(
          'label' => 'Flavor',
          'error' => false,
          'options' => $flavors
        ));
        echo $this->Form->input("Device.$psuedoId.region",array(
          'label' => 'Target',
          'error' => false,
          'options' => $regions
        ));
        ?>
      </div> <!-- /.provider -->
      <div class="configuration">
        <h2>Configuration</h2>
        <div class="accordion">
        <?php foreach($modulesAndVariables as $module){
          $moduleId = $module['id'];
          $moduleName = $module['shortName'];
          $variables = $module['variables'];
          ?>
          <h3><?php echo $moduleName; ?></h3>
          <div class="module">
            <?php foreach($variables as $var){
              $varId = $var['id'];
              $label = $var['name'];
              $description = htmlentities($var['description']);
              $inputName = "Device.$psuedoId.variables.$moduleId.$varId";
              $defaultValue = $var['default_value'];
              $required = $var['is_required'] ? true : false;
              ?>
              <div class="variable">
                <?php echo $this->Form->input($inputName,array(
                  'label' => array(
                    'text' => $label,
                    'class' => 'tooltip',
                    'title' => $description
                  ),
                  'default' => $defaultValue,
                  'required' => $required
                )); ?>
              </div>
            <?php } ?>
          </div> <!-- /.module -->
        <?php } ?>
        </div> <!-- /.accordion -->
      </div> <!-- /.configuration -->
    </div> <!-- /tab -->
  <?php } ?>
  </div> <!-- /.tabs -->
  <div class="clear"></div>
</div><!-- /.configure-devices -->

<?php $this->start('stepStyle'); ?>
  #configure-devices h2 {
    padding-bottom: 3px;
    border-bottom: 1px solid #ccc;
  }
  #configure-devices .tab > div {
    margin-bottom: 20px;
  }
  #configure-devices .configuration .filter {
    margin-bottom: 10px; 
  }
  #configure-devices nav a .name {
    display: block;
  }
  #configure-devices nav a.good .name:after {
    content: "\2714"
  }
  #configure-devices nav a.error .name:after {
    content: "\2718"
  }
<?php $this->end(); ?>

<?php $this->start('stepScript'); ?>
  $('.cta.primary').removeClass('disabled');
  $('.cta.primary').click('live',function(){
    var form = $(this).closest('form');
    $('.device-modal').css('display','none').appendTo(form);
    form.submit();
  });
<?php $this->end(); ?>
