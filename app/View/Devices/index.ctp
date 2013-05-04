<?php

$this->extend('/Common/standard');

$this->assign('title', 'Devices');

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
    'activityLogUri' => ''
));
$this->end();

//Main content
echo $this->StringsTable->datatable(
	'devices',							//Table ID
	'Current devices',					//Table title
	$deviceTableColumns,				//Column headings
	$_SERVER['REQUEST_URI'] . ".json",	//URI for pulling data
	15,									//Page length
	'Create device',					//CTA button txt
	'Create Device',					//CTA title
	'/Devices/create.json',				//CTA src
	true								//CTA enabled
);
