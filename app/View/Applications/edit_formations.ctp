<?php

echo $this->element('Datatables/default',array(
    'model' => 'formation',
	'tableTitle' => 'Current Application Formations',
    'tableColumns' => $formationTableColumns,
	'tableDataSrc' => "/Applications/edit_formations_data/$applicationId.json",
	'ctaButtonText' => 'Add formation',
	'ctaTitle' => 'Add Formation',
    'ctaEnabled' => true
));
