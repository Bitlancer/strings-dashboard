<?php

$deviceId = $device['Device']['id'];
$deviceName = $device['Device']['name'];

?>

<div id="reconfigure-loadbalancer">
  <ul id="notice"></ul>
  <form class="ajax" method="POST" action="<?php echo $this->here; ?>.json">
    <fieldset>
      <legend>Algorithm</legend>
      <?php
      echo $this->Form->input('algorithm', array(
        'div' => false,
        'label' => false,
        'default' => $algorithm,
        'options' => $algorithms
      ));
      ?>
    </fieldset>
    <fieldset>
      <legend>Protocol/Port</legend>
      <?php
      echo $this->Form->input('protocol', array(
        'div' => false,
        'label' => false,
        'class' => 'protocol',
        'default' => $protocol,
        'options' => $protocols
      ));
      ?>
      <span>/</span>
      <?php
      echo $this->Form->input('port', array(
        'div' => false,
        'label' => false,
        'class' => 'port',
        'default' => $port
      ));
      ?>
    </fieldset>
    <div class="submit">
      <a class="cta primary submit">Save</a>
      <a class="cta">Cancel</a>
    </div>
  </form>
<script>
  $('.protocol').change(function() {
    var protocolPortMap = <?php echo json_encode($protocolPortMap); ?>;
    var protocol = $(this).val();
    var port = protocolPortMap[protocol];
    $(this).parent().find('.port').val(port);
  });
</script>
</div>
