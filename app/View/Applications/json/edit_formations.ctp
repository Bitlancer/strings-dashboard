<?php

echo $this->element('Datatables/default',array(
    'model' => 'formation',
    'tableColumns' => $formationTableColumns,
	'tableDataSrc' => "/Applications/edit_formations_data/$applicationId.json",
    'ctaEnabled' => true
));
