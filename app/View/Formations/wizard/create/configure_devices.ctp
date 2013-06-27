<?php
$this->extend('/Formations/wizard/create/_template');

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
<?php $this->end(); ?>

<?php $this->start('stepScript'); ?>
  $('.cta.primary').removeClass('disabled');
  $('.cta.primary').click('live',function(){
    var form = $(this).closest('form');
    $('.device-modal').css('display','none').appendTo(form);
    form.submit();
  });
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
    $name = $device['name'];
    $blueprintPartName = $device['blueprintPartName'];
  ?>
  <tr>
    <td><?php echo $name; ?></td>
    <td><?php echo $blueprintPartName; ?></td>
    <td><?php echo $this->Strings->modalLink('Configure',"#device-$psuedoId",false,"Configure - $name",500,array('action')); ?></td>
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
  <?php
  echo $this->Form->input("Device.$psuedoId.flavor",array(
    'label' => 'Provider Flavor',
    'error' => false,
    'options' => $flavors
  ));
  echo $this->Form->input("Device.$psuedoId.region",array(
    'label' => 'Provider Target',
    'error' => false,
    'options' => $regions
  ));
  ?>
</div>
<?php } ?>
<!-- /modals -->

<?php
//Sidebar
$this->start('sidebar'); ?>
<?php $this->end(); ?>

