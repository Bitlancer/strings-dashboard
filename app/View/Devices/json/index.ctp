<?php

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($isAdmin){

        $modifiedOutputRow = $outputRow;

        //Info link on name column
        $modifiedOutputRow[0] = $view->Strings->link($modifiedOutputRow[0],"/Devices/view/" . $rawRow['Device']['id']);

        return $modifiedOutputRow;
    });
