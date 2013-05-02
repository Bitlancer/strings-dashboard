<?php

$this->extend('/Common/standard');

$this->assign('title', 'User Management');

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
	'activityLogUri' => ''
));
$this->end();

//Main content
echo $this->Strings->buildStringsDatatable(
	'users',					//Table ID
	'Current users',			//Table title
	$userTableColumns,			//Column headings
	$_SERVER['REQUEST_URI'] . ".json",	//URI for pulling data
	15,							//Page length
	'Create user',				//CTA button txt
	'Create User',				//CTA title
	'/Users/create.json',		//CTA src
	$userTableCTAEnabled		//CTA enabled
);
