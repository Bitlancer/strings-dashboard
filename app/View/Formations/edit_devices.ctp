<?php

$this->extend('/Common/standard');

$this->assign('title', $formation['Formation']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Formations/_activity_log');
$this->end();

//Main content
echo $this->element('Datatables/default',array(
    'model' => 'device',
    'columnHeadings' => $this->DataTables->getColumnHeadings(),
    'ctaEnabled' => true,
    'ctaSrc' => '/Formations/addDevice',
    'ctaModal' => false,
));

