<?php
$this->extend('/Formations/wizard/create/_template');

$this->assign('stepTitle','Select Device Counts');
?>

<?php $this->start('stepStyle'); ?>
  div.blueprint {
    margin-bottom:20px;
    padding-top:20px;
    border-top:1px solid #d6d6d6;
  }
  div.blueprint:nth-of-type(2){
    border-top:none;
  }
  div.blueprint .input {
    width:100%;
    margin-bottom:6px;
  }
  div.blueprint label {
    display:inline;
  }
  div.blueprint select {
    float:right;
    width:25%;
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
foreach($blueprintParts as $part){
  $id = $part['id'];
  $name = $part['name'];
  $descr = $part['description'];
  $min = intval($part['minimum']);
  $max = intval($part['maximum']);

  $countValues = array();
  for($x=$min;$x<=$max;$x++)
      $countValues[$x] = $x;
?>
<div class="blueprint">
  <?php
  echo $this->Form->input("blueprintPartCounts.$id",array(
    'label' => $name,
    'error' => false,
    'options' => $countValues 
  ));
  ?>
  <p><?php echo $descr; ?></p>
</div>
<?php } ?>

<?php
//Sidebar
$this->start('sidebar'); ?>
<?php $this->end(); ?>
