<?php

$this->extend('/Common/standard');

$this->assign('title', 'Formations');

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Formations/_activity_log');
$this->end();

//Main content
echo $this->element('Datatables/default',array(
    'model' => 'formation',
    'columnHeadings' => $formationTableColumns,
    'ctaEnabled' => true,
    'ctaSrc' => '/Formations/wizard',
    'ctaModal' => false,
));

