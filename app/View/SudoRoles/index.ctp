<?php

$this->extend('/Common/standard');

$this->assign('title', 'Sudo Roles');

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
    'activityLogUri' => ''
));
$this->end();

//Main content
echo $this->element('Datatables/default',array(
	'model' => 'sudoRole',
	'tableTitle' => 'Sudo roles',
    'tableColumns' => $sudoTableColumns,
	'ctaTitle' => 'Sudo Role',
	'ctaButtonText' => 'Create sudo role',
	'ctaSrc' => '/sudoRoles/create.json',
	'ctaWidth' => '500',
    'ctaEnabled' => true
));
