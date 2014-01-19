<?php

    if(!isset($title))
        $title = false;

    if(!isset($dataSrc))
        $dataSrc = $_SERVER['REQUEST_URI'] . '.json';

    if(!isset($emptyTableMsg))
        $emptyTableMsg = 'No data available';

    if(!isset($pageLength))
        $pageLength = DEFAULT_TABLE_PAGE_LENGTH;

    if(!isset($search))
        $search = false;

    if(!isset($processing))
        $processing = false;

    if(!isset($paginate))
        $paginate = true;

    if(!isset($refresh))
        $refresh = false;

    echo $this->StringsTable->datatable(
        $tableId,
        $columnHeadings,
        $dataSrc,
        array(
            'data-title' => $title,
            'data-length' => $pageLength,
            'data-empty-table' => $emptyTableMsg,
            'data-search' => ($search ? 'true' : 'false'),
            'data-processing' => ($processing ? 'true' : 'false'),
            'data-paginate' => ($paginate ? 'true' : 'false'),
            'data-refresh' => $refresh
        )
    );   
