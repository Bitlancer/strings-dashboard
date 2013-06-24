<?php

$this->extend('/Common/standard');

$this->assign('title',$profile['Profile']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Roles/_activity_log');
$this->end();
?>

<!-- Main content -->
<section>
<h2>Profile Details</h2>
<div id="profile-details">
  <?php
  echo $this->element('Tables/info',array(
    'info' => array(
      'Name' => $profile['Profile']['name'],
      'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$profile['Profile']['created'])
    )
  ));
  ?>
</div>
</section>

<section>
<h2>Modules</h2>
  <?php
    $modulesTableData = array();
    foreach($modules as $module){
        $name = $module['Module']['short_name'];
        $id = $module['Module']['id'];
        $modulesTableData[] = array(
            $this->Strings->link($name,'/Components/view/' . $id)
        );
    }
    echo $this->element('Tables/default',array(
      'columnHeadings' => array('Name'),
      'data' => $modulesTableData
    ));
  ?>
</section>

