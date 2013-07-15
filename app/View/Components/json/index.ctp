<?php

    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) use($isAdmin){

		//Info link on name column
        $outputRow[0] = $view->Strings->link($outputRow[0],"/Components/view/" . $rawRow['Module']['id']);

        return $outputRow;
    });
