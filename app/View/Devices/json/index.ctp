<?php

    echo $this->DataTables->render(
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
        $name = $modifiedOutputRow[0];
        $modifiedOutputRow[0] = $view->Strings->link($name,"/Devices/view/$deviceId");

        //Append action menu to last column
        $modifiedOutputRow[count($outputRow)-1] .= $actionMenu;

        //Add device status as a class to tr
        $modifiedOutputRow['DT_RowClass'] = "status status-$deviceStatus";

        return $modifiedOutputRow;
    });
