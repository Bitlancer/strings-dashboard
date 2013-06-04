<?php

$this->extend('/Common/standard');

$this->assign('title', 'Formations');

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
    'activityLogUri' => ''
));
$this->end();

//Main content
echo $this->element('Datatables/default',array(
    'model' => 'formation',
    'columnHeadings' => $formationTableColumns,
    'ctaEnabled' => true,
    'ctaSrc' => '/Formations/wizard',
    'ctaModal' => false,
));

