<?php

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($isAdmin){

        $actionMenu = $view->element('../Applications/_action_menu',array(
            'applicationId' => $rawRow['Application']['id'],
        ));

		//Info link on name column
        $outputRow[0] = $view->Strings->link($outputRow[0],"/Applications/view/" . $rawRow['Application']['id']);

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
    });
