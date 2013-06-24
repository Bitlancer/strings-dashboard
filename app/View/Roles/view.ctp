<?php

$this->extend('/Common/standard');

$this->assign('title',$role['Role']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Roles/_activity_log');
$this->end();
?>

<!-- Main content -->
<section>
<h2 class="float-left">Role Details</h2>
<h2 class="float-right">
  <?php
    echo $this->element('../Roles/_action_menu',array(
      'roleId' => $role['Role']['id'],
      'actionsDisabled' => !$isAdmin,
      'reload' => true
    ));
  ?>
</h2>
<hr class="clear" />
<div id="role-details">
  <?php
  echo $this->element('Tables/info',array(
    'info' => array(
      'Name' => $role['Role']['name'],
      'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$role['Role']['created'])
    )
  ));
  ?>
</div>
</section>

<section>
<h2>Profiles</h2>
  <?php
    $profilesTableData = array();
    foreach($profiles as $profile){
        $name = $profile['Profile']['name'];
        $id = $profile['Profile']['id'];
        $profilesTableData[] = array(
            $this->Strings->link($name,'/Profiles/view/' . $id)
        );
    }
    echo $this->element('Tables/default',array(
      'columnHeadings' => array('Name'),
      'data' => $profilesTableData
    ));
  ?>
</section>

