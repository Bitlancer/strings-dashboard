<?php
  $sidebarContent = $this->fetch('sidebar');
  $sidebarEmpty = empty($sidebarContent);
?>
<h1><?php echo $title_for_layout; ?></h1>
<div id="content-wrapper" class="<?php echo !$sidebarEmpty ? "columns" : ""; ?>">
<div id="create-formation">
  <?php echo $this->element('messages'); ?>
  <section>
    <h2><?php echo $this->Wizard->stepNumber() . ". " . $this->fetch('stepTitle'); ?></h2>
    <?php echo $this->Form->create('Formation',array(
      'url' => $this->here,
      'class' => 'vertical-labels'
    ));
    ?>
    <div>
      <?php echo $this->fetch('content'); ?>
    </div>
    <div class="submit">
      <a class="cta primary disabled"><?php echo $this->fetch('forwardButtonText','Next step'); ?></a>
      <a class="cta wizard quit"><?php echo $this->fetch('backButtonText','Cancel'); ?></a>
      <div class="step-description">
        Step <?php echo $this->Wizard->stepNumber(); ?> of <?php echo $this->Wizard->stepTotal(); ?>
      </div>
    </div>
  <?php echo $this->Form->end(); ?>
  <script>
    $('.cta.wizard.quit').click('live',function(){
      var form = $(this).closest('form');
      form.append('<input type="hidden" name="data[Cancel]" />');
      form.submit();
    });
  <?php echo $this->fetch('stepScript'); ?>
  </script>
  </section>
</div>
<div>
  <?php echo $sidebarContent ?>
</div>
<hr class="clear">
</div> <!-- /#content-wrapper -->
