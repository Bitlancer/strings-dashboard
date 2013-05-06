<?php

$model = strtolower($model);
$modelPlural = Inflector::pluralize($model);

if(!isset($tableTitle))
	$tableTitle = 'Current ' . $modelPlural;

if(!isset($tableDataSrc))
    $tableDataSrc = $_SERVER['REQUEST_URI'] . '.json';

if(!isset($tablePageLength))
    $tablePageLength = DEFAULT_TABLE_PAGE_LENGTH;

if(!isset($ctaButtonText))
	$ctaButtonText = 'Create ' . $model;

if(!isset($ctaTitle))
	$ctaTitle = 'Create ' . ucfirst($model);

if(!isset($ctaSrc))
	$ctaSrc = "/$modelPlural/create.json"; 

if(!isset($ctaWidth))
	$ctaWidth = 360;

if(!isset($ctaDisabled))
	$ctaDisabled = false;

echo $this->StringsTable->datatable(
    $model,			                    //Table ID
    $tableTitle, 				        //Table title
    $tableColumns,           			//Column headings
    $tableDataSrc,  					//URI for pulling data
    $tablePageLength, 			        //Page length
    $ctaButtonText,   	            	//CTA button txt
    $ctaTitle,					        //CTA title
    $ctaSrc,					        //CTA src
	$ctaWidth,							//CTA width - used for modal width
    $ctaDisabled                        //CTA Disabled
);
