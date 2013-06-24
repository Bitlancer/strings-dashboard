<?php

$this->extend('/Common/standard');

$this->assign('title',$module['Module']['short_name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Roles/_activity_log');
$this->end();
?>

<!-- Main content -->
<section>
<h2>Module Details</h2>
<div id="module-details">
  <?php
    echo $this->element('Tables/info',array(
      'info' => array(
        'Name' => $module['Module']['short_name'],
        'Source Type' => $module['ModuleSource']['type'],
        'Source' => $module['ModuleSource']['name'],
        'Source Reference' => $module['Module']['reference'],
        'Source Path' => $module['Module']['path'],
        'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$module['Module']['created'])
      )
    ));
  ?>
</div>
</section>
