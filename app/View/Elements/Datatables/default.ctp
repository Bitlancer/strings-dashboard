<?php

$model = strtolower($model);
$modelPlural = Inflector::pluralize($model);

if(!isset($title))
	$title = 'Current ' . $modelPlural;

if(!isset($dataSrc))
    $dataSrc = $_SERVER['REQUEST_URI'] . '.json';

if(!isset($pageLength))
    $pageLength = DEFAULT_TABLE_PAGE_LENGTH;

if(!isset($ctaModal))
    $ctaModal = true;

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

echo $this->StringsTable->datatableWithCta(
    $model,			                    //Table ID
    $title, 				            //Table title
    $columnHeadings,           			//Column headings
    $dataSrc,  					        //URI for pulling data
    $pageLength, 			            //Page length
    $ctaModal,                          //Is CTA a modal or a link
    $ctaButtonText,   	            	//CTA button txt
    $ctaSrc,                            //Modal src or link href
    $ctaDisabled,                       //CTA disabled
    $ctaTitle,					        //CTA title
	$ctaWidth   						//CTA width - used for modal width
);
