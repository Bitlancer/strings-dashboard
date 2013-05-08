<?php

$model = strtolower($model);
$modelPlural = Inflector::pluralize($model);

if(!isset($tableTitle))
	$tableTitle = 'Current ' . $modelPlural;

if(!isset($tableDataSrc))
    $tableDataSrc = $_SERVER['REQUEST_URI'] . '.json';

if(!isset($tablePageLength))
    $tablePageLength = DEFAULT_TABLE_PAGE_LENGTH;

echo $this->StringsTable->simpleDatatable(
    $model,			                    //Table ID
    $tableTitle, 				        //Table title
    $tableColumns,           			//Column headings
    $tableDataSrc,  					//URI for pulling data
    $tablePageLength 			        //Page length
);
