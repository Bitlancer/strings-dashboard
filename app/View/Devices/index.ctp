<?php

$this->extend('/Common/standard');

$this->assign('title', 'Devices');

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Devices/_activity_log');
$this->end();

//Main content
echo $this->element('Datatables/default',array(
    'model' => 'device',
    'columnHeadings' => $this->DataTables->getColumnHeadings(),
    'ctaDisabled' => $createCTADisabled
));
