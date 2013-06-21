<?php

if(!isset($title))
    $title = false;

if(!isset($data))
    $data = array();

if(!isset($search))
    $search = true;

if(!isset($pageLength))
    $pageLength = DEFAULT_TABLE_PAGE_LENGTH;

echo $this->StringsTable->datatable(
    $tableId,                        //Table ID
    $columnHeadings,                 //Column headings
    $data,                           //URI for pulling data
    $pageLength,                     //Page length
    $title,                          //Table title
    false,                           //CTA element
    false                            //Whether the search field is present
);
