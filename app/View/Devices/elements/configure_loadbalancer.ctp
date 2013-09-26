<?php
  if(!isset($inputPrefix))
    $inputPrefix = "";
  else
    $inputPrefix .= ".";
?>
<div id="configure-loadbalancer">
<?php
  echo $this->Form->input("${inputPrefix}virtualIpType",array(
    'label' => array(
      'text' => 'Network',
      'class' => 'tooltip',
      'title' => 'Select public if you are a load-balancing a public service. Select servicenet if you are load-balancing a private service.'
    ),
    'options' => $virtualIpTypes,
  ));
  ?>
  <div id="protocol-port" class="input select">
    <?php
    echo $this->Form->input("${inputPrefix}protocol",array(
      'id' => 'protocol',
      'div' => false,
      'label' => array(
        'text' => 'Protocol/port',
      ),
      'default' => 'HTTP',
      'options' => $protocols
    ));
    ?>
    <span>/</span>
    <?php
    echo $this->Form->input("${inputPrefix}port",array(
      'id' => 'port',
      'div' => false,
      'label' => false,
      'default' => '80'
    ));
    ?>
  </div>
  <?php
  echo $this->Form->input("${inputPrefix}algorithm",array(
    'label' => array(
      'text' => 'Algorithm',
      'class' => 'tooltip',
      'title' => 'The algorithm this load-balancer will use for distributing requests to its nodes.',
    ),
    'options' => $algorithms
  ));
?>
</div>
<script>
  var protocolPortMap = <?php echo json_encode($protocolPortMap); ?>;
</script>
<script>
  $('#protocol').change(function() {
    var protocol = $(this).val();
    var port = protocolPortMap[protocol];
    $('#port').val(port);
  });
</script>
