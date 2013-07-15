<?php

    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) use($isAdmin){

        $actionMenu = $view->element('../Formations/_devices_action_menu',array(
            'deviceId' => $rawRow['Device']['id'],
            'formationId' => $rawRow['Device']['formation_id'],
            'actionsDisabled' => (!$isAdmin || $rawRow['Device']['status'] !== 'active')
        ));

		//Info link on name column
        $outputRow[0] = $view->Strings->link($outputRow[0],"/Devices/view/" . $rawRow['Device']['id']);

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
    });
