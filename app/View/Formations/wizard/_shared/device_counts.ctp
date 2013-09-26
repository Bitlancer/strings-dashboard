<?php
$this->extend('/Formations/wizard/create/_template');

$this->assign('stepTitle','Select Device Counts');
?>

<div id="device-counts">
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
</div> <!-- /#device-counts -->

<?php $this->start('stepScript'); ?>
  $('.cta.primary').removeClass('disabled').addClass('submit');
<?php $this->end(); ?>
