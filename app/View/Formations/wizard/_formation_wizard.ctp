<h1>Create Formation</h1>
<div class="columns">
<div>
  <?php echo $this->element('messages'); ?>
  <style>
    h3 {
      font-weight:bold;
    }
    div.submit {
      margin-top:20px;
    }
    div.submit .step-description {
      float:right;
      margin-right:10px;
      line-height:30px;
      color:#999;
    }
    <?php echo $this->fetch('stepStyle'); ?>
  </style>
  <section>
  </style>
  <h2><?php echo $this->fetch('stepNumber') . ". " . $this->fetch('stepTitle'); ?></h2>
  <form method="post" action="<?php echo $this->here; ?>">
    <?php echo $this->fetch('content'); ?>
    <div class="submit">
      <a class="cta primary disabled"><?php echo $this->fetch('forwardButtonText','Next step'); ?></a>
      <a class="cta wizard quit"><?php echo $this->fetch('backButtonText','Cancel'); ?></a>
    <div class="step-description">Step <?php echo $this->fetch('stepNumber'); ?> of 4</div>
  </form>
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
  <?php echo $this->fetch('sidebar'); ?>
</div>
<hr class="clear">
</div> <!-- /.columns -->
