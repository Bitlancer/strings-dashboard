<?php

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($isAdmin){

		//$outputRow[0] = $view->Strings->modalLink($outputRow[0],"/Applications/view/1.json");

		//$outputRow[0] = $outputRow[0] . $view->Strings->link('Remove',"/Applications/remove_formation/1/1.json");

        return $outputRow;
    });
