<?php

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($isAdmin){

        $actionMenu = $view->element('../Formations/_action_menu',array(
            'formationId' => $rawRow['Formation']['id'],
            'actionsDisabled' => (!$isAdmin || $rawRow['Formation']['status'] !== 'active')
        ));

		//Info link on name column
        $outputRow[0] = $view->Strings->link($outputRow[0],"/Formations/view/" . $rawRow['Formation']['id']);		

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
    });
