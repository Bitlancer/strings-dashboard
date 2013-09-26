<?php

$this->extend('/Common/standard');

$this->assign('title', 'User Management');

//Set sidebar content
$this->start('sidebar');
echo $this->element('../SudoRoles/_activity_log');
$this->end();

//Main content
echo $this->element('Datatables/default',array(
	'model' => 'sudoRole',
	'title' => 'Sudo roles',
    'columnHeadings' => $this->DataTables->getColumnHeadings(),
	'ctaTitle' => 'Sudo Role',
	'ctaButtonText' => 'Create sudo role',
	'ctaSrc' => '/sudoRoles/create',
	'ctaWidth' => '500',
    'ctaDisabled' => $createCTADisabled
));
