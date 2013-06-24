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
<h2>Profiles</h2>
  <?php
    $modulesTableData = array();
    foreach($modules as $module){
        $name = $module['Module']['name'];
        $id = $module['Module']['id'];
        $modulesTableData[] = array(
            $this->Strings->link($name,'/Modules/view/' . $id)
        );
    }
    echo $this->element('Tables/default',array(
      'columnHeadings' => array('Module'),
      'data' => $modulesTableData
    ));
  ?>
</section>

