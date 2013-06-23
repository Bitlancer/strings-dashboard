<?php

$this->extend('/Common/standard');

$this->assign('title', 'User Management');

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Users/_activity_log');
$this->end();

//Main content
echo $this->element('Datatables/default',array(
	'model' => 'user',
	'columnHeadings' => $userTableColumns,
    'ctaDisabled' => $createCTADisabled
));
