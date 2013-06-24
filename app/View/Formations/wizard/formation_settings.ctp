<?php
$this->extend('/Formations/wizard/_formation_wizard');

$this->assign('stepNumber','1');
$this->assign('stepTitle','Formation Options');
?>

<?php $this->start('stepStyle'); ?>
<?php $this->end(); ?>

<?php $this->start('stepScript'); ?>
  $('.cta.primary').removeClass('disabled').addClass('submit');
<?php $this->end(); ?>

<?php
  echo $this->Form->input('Formation.name',array(
    'label' => 'Formation Name',
    'error' => false,
  ));
  echo $this->Form->input('Implementation.id',array(
    'label' => 'Infrastructure Provider',
    'error' => false,
    'options' => $implementations
  ));
  echo $this->Form->input('Dictionary.id',array(
    'label' => 'Device Name Dictionary',
    'error' => false,
    'options' => $dictionaries
  ));
?>

<?php
//Sidebar
$this->start('sidebar'); ?>
<?php $this->end(); ?>
