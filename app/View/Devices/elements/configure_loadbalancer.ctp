<?php
  if(!isset($inputPrefix))
    $inputPrefix = "";
  else
    $inputPrefix .= ".";
?>
<div id="configure-loadbalancer">
  <div class="virtual-ip input select">
  <?php
  echo $this->Form->input("${inputPrefix}virtualIpType",array(
    'div' => false,
    'class' => 'virtualIpType',
    'label' => array(
      'text' => 'Virtual IP',
      'class' => 'tooltip',
      'title' => 'Select public if you are a load-balancing a public service ' . 
                'or servicenet if you are load-balancing a private service.'

    ),
    'options' => $virtualIpTypes,
  ));
  ?>
  </div> <!-- /.virtual-ip -->
  <?php
  echo $this->Form->input("${inputPrefix}algorithm",array(
    'label' => array(
      'text' => 'Algorithm',
      'class' => 'tooltip',
      'title' => 'The algorithm this load-balancer will use for distributing requests to its nodes.',
    ),
    'options' => $algorithms
  ));
  echo $this->Form->input("${inputPrefix}sessionPersistence",array(
    'label' => array(
      'text' => 'Session Persistence',
      'class' => 'tooltip',
      'title' => 'Forces multiple requests, of the same protocol, from ' .
                'clients to be directed to the same node.',
    ),
    'options' => array(0 => 'Disabled', 1 => 'Enabled'),
  ));
  ?>
  <div class="protocols-ports">
    <?php $lbProtocolClass = str_replace(".","_","${inputPrefix}protocol"); ?>
    <div class="protocol-port input select">
      <?php
      echo $this->Form->input("${inputPrefix}protocol.0",array(
        'class' => "protocol $lbProtocolClass",
        'div' => false,
        'label' => 'Protocol/port',
        'default' => 'HTTP',
        'options' => $protocols
      ));
      ?>
      <span>/</span>
      <?php
      echo $this->Form->input("${inputPrefix}port.0",array(
        'class' => 'port',
        'div' => false,
        'label' => false,
        'default' => '80'
      ));
      ?>
    </div> <!-- /.protocol-port -->
    <div class="protocol-port input select">
      <?php
      echo $this->Form->input("${inputPrefix}protocol.1",array(
        'class' => "protocol $lbProtocolClass",
        'div' => false,
        'label' => array(
          'text' => 'Additional protocol/port',
          'class' => 'tooltip',
          'title' => 'Load-balance a second protocol/port pair by specifying ' .
                    'an additional protocol/port pair here.'
        ),
        'options' => $protocols,
        'empty' => 'None',
        'default' => 'HTTPS'
      ));
      ?>
      <span>/</span>
      <?php
      echo $this->Form->input("${inputPrefix}port.1",array(
        'class' => 'port',
        'div' => false,
        'label' => false,
        'empty' => '',
        'default' => '443'
      ));
      ?>
    </div> <! -- /.protocol-port -->
  </div> <!-- /.protocols-ports -->
</div>
<script>
  var lbProtocolClass = ".<?php echo "$lbProtocolClass"; ?>";
  $(lbProtocolClass).change(function() {
    var protocolPortMap = <?php echo json_encode($protocolPortMap); ?>;
    var protocol = $(this).val();
    var port = protocolPortMap[protocol];
    $(this).parent().find('.port').val(port);
  });
</script>
