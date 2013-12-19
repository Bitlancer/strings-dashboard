<?php

$deviceId = $device['Device']['id'];
$deviceName = $device['Device']['name'];

?>
<div id="manage-nodes">
  <ul id="notice"></ul>
  <form class="ajax" method="POST" action="<?php echo $this->here; ?>.json">
    <?php
      echo $this->element('Associations/fieldset',array(
        'fieldsetTitle' => 'Nodes',
        'memberData' => $nodes,
        'memberAutocompleteSrc' => false,
        'memberFieldName' => 'data[nodes][]',
        'emptyTableMessage' => 'Add a node above',
        'inputPlaceholder' => 'Node IP Address'
      ));
    ?>
    <div class="submit">
      <a class="cta primary submit">Save</a>
      <a class="cta">Cancel</a>
    </div>
  </form>
</div>
