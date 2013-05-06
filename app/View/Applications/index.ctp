<?php

$this->extend('/Common/standard');

$this->assign('title', 'Applications');

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
    'activityLogUri' => ''
));
$this->end();

//Main content
echo $this->element('Datatables/default',array(
    'model' => 'application',
    'tableColumns' => $applicationTableColumns,
    'ctaEnabled' => true
));
