<?php

    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) use($isAdmin){

        $formationId = $rawRow['Formation']['id'];
        $formationStatus = $rawRow['Formation']['status'];

        $actionMenu = $view->element('../Formations/elements/action_menu',array(
            'formationId' => $formationId,
            'actionsDisabled' => (!$isAdmin || $formationStatus !== 'active')
        ));

		//Info link on name column
        $name = $outputRow[0];
        $outputRow[0] = $view->Strings->link($name,"/Formations/view/$formationId");

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        //Add formation status as a class
        $outputRow['DT_RowClass'] = "status status-$formationStatus";

        return $outputRow;
    });
