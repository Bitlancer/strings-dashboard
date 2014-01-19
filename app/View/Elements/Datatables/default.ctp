<?php

    $model = strtolower($model);
    $modelPlural = Inflector::pluralize($model);

    if(!isset($dataSrc))
        $dataSrc = $_SERVER['REQUEST_URI'] . '.json';

    if(!isset($emptyTableMsg))
        $emptyTableMsg = 'No data available';

    if(!isset($pageLength))
        $pageLength = DEFAULT_TABLE_PAGE_LENGTH;

    if(!isset($tableId))
        $tableId = $model;

    if(!isset($ctaModal))
        $ctaModal = true;

    if(!isset($title) && !isset($rawTitle))
        $title = "Current $modelPlural";

    if(!isset($search))
        $search = true;

    if(!isset($processing))
        $processing = false;

    if(!isset($paginate))
        $paginate = true;

    if(!isset($refresh))
        $refresh = false;

    $dataTableOptions = array(
        'data-length' => $pageLength,
        'data-empty-table' => $emptyTableMsg,
        'data-search' => ($search ? 'true' : 'false'),
        'data-processing' => ($processing ? 'true' : 'false'),
        'data-paginate' => ($paginate ? 'true' : 'false'),
        'data-refresh' => $refresh
    );

    if(isset($title))
        $dataTableOptions['data-title'] = $title;

    if(isset($rawTitle))
        $dataTableOptions['data-raw-title'] = $rawTitle;

    if(isset($noCta) && $noCta){

        echo $this->StringsTable->datatable(
            $tableId,
            $columnHeadings,
            $dataSrc,
            $dataTableOptions
        );

    }
    else {
        if(!isset($ctaButtonText))
            $ctaButtonText = "Create $model";

        if(!isset($ctaTitle))
            $ctaTitle = 'Create ' . ucfirst($model);

        if(!isset($ctaSrc))
            $ctaSrc = "/$modelPlural/create"; 

        if(!isset($ctaClasses))
            $ctaClasses = array();

        if(!isset($ctaWidth))
            $ctaWidth = 360;

        if(!isset($ctaDisabled))
            $ctaDisabled = false;

        echo $this->StringsTable->datatableWithCta(
            $tableId,		                    //Table ID
            $columnHeadings,           			//Column headings
            $dataSrc,  					        //URI for pulling data
            $dataTableOptions,                  //Data table options like paginate, search, etc
            $ctaModal,                          //Is CTA a modal or a link
            $ctaSrc,                            //Modal src or link href
            $ctaButtonText,                     //CTA button txt
            $ctaDisabled,                       //CTA disabled
            $ctaClasses,                        //Additional classes to add to the CTA
            $ctaTitle,					        //CTA title
            $ctaWidth   						//CTA width - used for modal width
        );
    }

