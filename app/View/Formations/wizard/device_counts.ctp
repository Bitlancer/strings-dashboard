<?php
$this->extend('/Formations/wizard/_formation_wizard');

$this->assign('stepNumber','2');
$this->assign('stepTitle','Select Device Counts');
?>

<?php $this->start('stepStyle'); ?>
  div.blueprint {
    margin-bottom:20px;
    padding-top:20px;
    border-top:1px solid #d6d6d6;
  }
  div.blueprint:first-of-type {
    margin-top:20px;
    border-top:none;
  }
  div.blueprint h2 {
    display:inline;
    width:75%;
  }
  div.blueprint select {
    display:block;
    float:right;
    width:24%;
  }
  div.blueprint p {
    font-size: 13px;
    margin-top:8px;
  }
<?php $this->end(); ?>

<?php $this->start('stepScript'); ?>
  $('.cta.primary').removeClass('disabled').addClass('submit');
<?php $this->end(); ?>

<?php
//Main content
foreach($blueprintParts as $part){ 
  $id = $part['BlueprintPart']['id'];
  $name = $part['BlueprintPart']['name'];
  $descr = $part['BlueprintPart']['description'];
  $min = intval($part['BlueprintPart']['minimum']);
  $max = intval($part['BlueprintPart']['maximum']);
?>
<div class="blueprint">
  <h3><?php echo $name; ?></h3>
  <select name="data[BlueprintPart][<?php echo $id; ?>][count]" required>
    <?php for($x=$min;$x<=$max;$x++) {
      echo "<option value=\"$x\">$x</option>";
    } ?>
  </select>
  <p><?php echo $descr; ?></p>
</div>
<?php } ?>

<?php
//Sidebar
$this->start('sidebar'); ?>
<h2>Help</h2>
<?php $this->end(); ?>
