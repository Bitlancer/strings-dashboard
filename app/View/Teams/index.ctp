<?php

$this->extend('/Common/standard');

$this->assign('title', 'User Management');

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Teams/_activity_log');
$this->end();

//Main content
echo $this->element('Datatables/default',array(
	'model' => 'team',
	'columnHeadings' => $teamTableColumns,
	'ctaEnabled' => $teamTableCTAEnabled
));
