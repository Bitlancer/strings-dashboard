<?php

$this->extend('/Common/standard');

$this->assign('title', 'Applications');

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Applications/_elements/activity_log');
$this->end();

//Main content
echo $this->element('Datatables/default',array(
    'tableId' => 'applications',
    'model' => 'application',
    'columnHeadings' => $this->DataTables->getColumnHeadings(),
    'ctaDisabled' => $createCTADisabled
));
