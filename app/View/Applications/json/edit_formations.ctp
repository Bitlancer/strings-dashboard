<?php
    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($isAdmin){

        $formationId = $rawRow['Formation']['id'];

        $outputRow[count($outputRow)-1] .= "<a class=\"action remove\" data-id=\"$formationId\">Remove</a>";
        return $outputRow;
    });
