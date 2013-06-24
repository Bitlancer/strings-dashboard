<?php

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($isAdmin){

        $actionMenu = $view->element('../Roles/_action_menu',array(
            'roleId' => $rawRow['Role']['id'],
            'actionsDisabled' => !$isAdmin
        ));

		//Info link on name column
        $outputRow[0] = $view->Strings->link($outputRow[0],"/Roles/view/" . $rawRow['Role']['id']);

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
    });
