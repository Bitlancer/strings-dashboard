<?php

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($isAdmin){

        $deviceId = $rawRow['Device']['id'];
        $deviceStatus = $rawRow['Device']['status'];
        $formationId = $rawRow['Device']['formation_id'];

        $modifiedOutputRow = $outputRow;

        $actionMenu = $view->element('../Formations/_devices_action_menu',array(
            'deviceId' => $deviceId,
            'formationId' => $formationId,
            'actionsDisabled' => (!$isAdmin || $deviceStatus != 'active')
        ));

        //Info link on name column
        $modifiedOutputRow[0] = $view->Strings->link($modifiedOutputRow[0],"/Devices/view/$deviceId");

        //Append action menu to last column
        $modifiedOutputRow[count($outputRow)-1] .= $actionMenu;

        return $modifiedOutputRow;
    });
