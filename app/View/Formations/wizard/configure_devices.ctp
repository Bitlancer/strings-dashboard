<?php
$this->extend('/Formations/wizard/_formation_wizard');

$this->assign('stepNumber','4');
$this->assign('stepTitle','Configure Devices');
$this->assign('forwardButtonText','Complete');
?>

<?php $this->start('stepStyle'); ?>
  table#configure-devices td {
    border:none; 
  }
  div.device-modal {
    display:none;
  }
  div.device-modal .option {
    margin-bottom:20px;
  }
  div.device-modal .option h3 {
    margin-bottom:6px;
  }
<?php $this->end(); ?>

<?php $this->start('stepScript'); ?>
  $('.cta.primary').removeClass('disabled').addClass('submit');
<?php $this->end(); ?>

<table id="configure-devices">
<thead>
  <tr>
  <th>Name</th>
  <th>Type</th>
  <th></th>
</thead>
<tbody>
<?php foreach($devices as $device){
    $psuedoId = $device['psuedoId'];
    $blueprintPartId = $device['blueprintPartId'];
    $dictionaryWordId = $device['dictionaryWordId'];
    $name = $dictionaryWords[$dictionaryWordId]['DictionaryWord']['word'];
    $role = $blueprintParts[$blueprintPartId]['BlueprintPart']['name'];
  ?>
  <tr>
    <td><?php echo $name; ?></td>
    <td><?php echo $role; ?></td>
    <td><?php echo $this->Strings->modalLink('Configure',"#device-$psuedoId",false,'Configure',500,array('action')); ?></td>
  </tr>
<?php } ?>
</tbody>
</table>

<!-- modals for configuring each device -->
<?php
foreach($devices as $device){
  $psuedoId = $device['psuedoId'];
?>
<div class="device-modal" id="device-<?php echo $psuedoId; ?>">
  <div class="option">
    <h3>Provider Target</h3>
    <select name="data[Device][<?php echo $psuedoId; ?>]">
      <?php foreach($regions as $region) {
        $name = $region['custom_name'];
        ?>
        <option value="<?php echo $name; ?>"><?php echo $name; ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<?php } ?>
<!-- /modals -->

<?php
//Sidebar
$this->start('sidebar'); ?>
<h2>Help</h2>
<?php $this->end(); ?>

