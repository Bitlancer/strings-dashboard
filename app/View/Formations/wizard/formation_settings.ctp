<?php
$this->extend('/Formations/wizard/_formation_wizard');

$this->assign('stepNumber','3');
$this->assign('stepTitle','Formation Options');
?>

<?php $this->start('stepStyle'); ?>
  div.input {
    margin-bottom:20px;  
  }
  div.input h3 {
    margin-bottom:6px;
  }
<?php $this->end(); ?>

<?php $this->start('stepScript'); ?>
  $('.cta.primary').removeClass('disabled').addClass('submit');
<?php $this->end(); ?>

<div class="input">
  <h3>Infrastructure Provider</h3>
  <select name="data[Implementation][id]">
    <?php
      foreach($implementations as $i){
        $id = $i['Implementation']['id'];
        $name = $i['Implementation']['name'];
        ?>
        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
      <?php } ?>
  </select>
</div>

<div class="input">
  <h3>Device Name Dictionary</h3>
  <select name="data[Dictionary][id]">
    <?php
      foreach($dictionaries as $dict){
        $id = $dict['Dictionary']['id'];
        $name = $dict['Dictionary']['name'];
        ?>
        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
      <?php } ?>
  </select>
</div>

<?php
//Sidebar
$this->start('sidebar'); ?>
<h2>Help</h2>
<?php $this->end(); ?>
