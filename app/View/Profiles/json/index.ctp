<?php

    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) use($isAdmin){

		//Info link on name column
        $outputRow[0] = $view->Strings->link($outputRow[0],"/Profiles/view/" . $rawRow['Profile']['id']);

        return $outputRow;
    });
