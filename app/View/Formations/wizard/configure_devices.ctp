<?php
$this->extend('/Formations/wizard/_formation_wizard');

$this->assign('stepNumber','4');
$this->assign('stepTitle','Configure Devices');
$this->assign('forwardButtonText','Complete');
?>

<?php $this->start('stepStyle'); ?>
<?php $this->end(); ?>

<?php $this->start('stepScript'); ?>
  $('.cta.primary').removeClass('disabled').addClass('submit');
<?php $this->end(); ?>


<?php
//Sidebar
$this->start('sidebar'); ?>
<h2>Help</h2>
<?php $this->end(); ?>

