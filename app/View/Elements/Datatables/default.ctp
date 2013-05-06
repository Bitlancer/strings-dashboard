<?php

$model = strtolower($model);
$modelPlural = Inflector::pluralize($model);

if(!isset($tableDataSrc))
	$tableDataSrc = $_SERVER['REQUEST_URI'] . '.json';

if(!isset($ctaSrc))
	$ctaSrc = "/$modelPlural/create.json"; 

if(!isset($tablePageLength))
	$tablePageLength = DEFAULT_TABLE_PAGE_LENGTH;

echo $this->StringsTable->datatable(
    $model,			                    //Table ID
    'Current ' . $modelPlural,          //Table title
    $tableColumns,           			//Column headings
    $tableDataSrc,  					//URI for pulling data
    $tablePageLength, 			        //Page length
    'Create ' . $model,               	//CTA button txt
    'Create ' . ucfirst($model),        //CTA title
    $ctaSrc,					        //CTA src
    $ctaEnabled                         //CTA enabled
);
