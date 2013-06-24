<?php

$this->extend('/Common/standard');

$this->assign('title', 'Roles');

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Applications/_activity_log');
$this->end();

//Main content
echo $this->element('Datatables/default',array(
    'model' => 'role',
    'columnHeadings' => $columnHeadings,
    'noCta' => true
));
