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
echo $this->Strings->buildStringsDatatable(
	'applications',						//Table ID
	'Current applications',				//Table title
	$applicationTableColumns,			//Column headings
	$_SERVER['REQUEST_URI'] . ".json",	//URI for pulling data
	15,									//Page length
	'Create application',				//CTA button txt
	'Create Application',				//CTA title
	'/Applications/create.json',		//CTA src
	true								//CTA enabled
);
