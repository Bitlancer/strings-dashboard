<?php
$this->extend('/Formations/wizard/create/_template');

$this->assign('stepTitle','Formation Options');
?>

<div id="formation-settings">
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
    echo $this->Form->input('Environment.id',array(
      'label' => 'Environment',
      'error' => false,
      'options' => $environments
    ));
  ?>
</div> <!-- /#formation-settings -->

<?php $this->start('stepScript'); ?>
  $('.cta.primary').removeClass('disabled').addClass('submit');
<?php $this->end(); ?>
