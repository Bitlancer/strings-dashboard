<?php
$this->extend('/Formations/wizard/create/_template');

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
    <td><?php echo $this->Strings->oldModalLink('Configure',"#device-$psuedoId",false,"Configure - $name",500,array('action')); ?></td>
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
  </div>
  <div class="configuration">
    <h2>Configuration</h2>
    <div class="filter">
      <label>Filter Variables
        <select>
          <option value="required">Required</option>
          <option value="all">All</option>
        </select>
      </label>
    </div>
    <div class="accordion">
    <h3>NTP</h3>
    <div class="module">
      <div class="variable">
        <label>Server Address
          <input type="text" />
        </label>
      </div>
      <div class="variable">
        <label>Server Address
          <input type="text" />
        </label>
      </div>
      <div class="variable">
        <label>Server Address
          <input type="text" />
        </label>
      </div>
      <div class="variable">
        <label>Server Address
          <input type="text" />
        </label>
      </div>
    </div>
    <h3>MySQL</h3>
    <div class="module">
      <div class="variable">
        <label>InnoDB Buffer Pool Size
          <input type="text" />
        </label>
      </div>
      <div class="variable">
        <label>Server Address
          <input type="text" />
        </label>
      </div>
      <div class="variable">
        <label>Server Address
          <input type="text" />
        </label>
      </div>
      <div class="variable">
        <label>Server Address
          <input type="text" />
        </label>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<!-- /modals -->

<?php
//Sidebar
$this->start('sidebar'); ?>
<?php $this->end(); ?>

