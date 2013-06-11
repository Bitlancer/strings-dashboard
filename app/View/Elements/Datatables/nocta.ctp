<?php

$model = strtolower($model);
$modelPlural = Inflector::pluralize($model);

if(!isset($title))
    $title = 'Current ' . $modelPlural;

if(!isset($dataSrc))
    $dataSrc = $_SERVER['REQUEST_URI'] . '.json';

if(!isset($search))
    $search = true;

if(!isset($pageLength))
    $pageLength = DEFAULT_TABLE_PAGE_LENGTH;

echo $this->StringsTable->datatable(
    $model,                        //Table ID
    $title,                        //Table title
    $columnHeadings,               //Column headings
    $dataSrc,                      //URI for pulling data
    $pageLength,                   //Page length
    false,                          //CTA element
    $search                         //Whether the search field is present
);
