<?php

$this->extend('/Common/standard');

$this->assign('title', 'Profiles');

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Roles/_activity_log');
$this->end();

//Main content
echo $this->element('Datatables/default',array(
    'model' => 'profile',
    'columnHeadings' => $columnHeadings,
    'noCta' => true
));
