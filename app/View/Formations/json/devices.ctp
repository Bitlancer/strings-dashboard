<?php

    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) use($isAdmin){

        $deviceId = $rawRow['Device']['id'];
        $formationId = $rawRow['Device']['formation_id'];
        $deviceStatus = $rawRow['Device']['status'];

        $actionMenu = $view->element('../Formations/_devices_action_menu',array(
            'deviceId' => $deviceId,
            'formationId' => $formationId,
            'actionsDisabled' => (!$isAdmin || $deviceStatus !== 'active')
        ));

		//Info link on name column
        $name = $outputRow[0];
        $outputRow[0] = $view->Strings->link($name,"/Devices/view/" . $deviceId);

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        //Add device status as a class
        $outputRow['DT_RowClass'] = "status status-$deviceStatus";

        return $outputRow;
    });
