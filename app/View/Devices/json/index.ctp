<?php

    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) use($isAdmin){

        $deviceId = $rawRow['Device']['id'];
        $deviceType = $rawRow['DeviceType']['name'];
        $deviceStatus = $rawRow['Device']['status'];
        $formationId = $rawRow['Device']['formation_id'];

        $modifiedOutputRow = $outputRow;

        if($deviceType == 'instance'){
            $actionMenu = $view->element('../Devices/elements/instance_action_menu',array(
                'deviceId' => $deviceId,
                'formationId' => $formationId,
                'actionsDisabled' => (!$isAdmin || $deviceStatus != 'active')
            ));
        }
        elseif($deviceType = 'load-balancer'){
            $actionMenu = $view->element('../Devices/elements/loadbalancer_action_menu',array(
                'deviceId' => $deviceId,
                'formationId' => $formationId,
                'actionsDisabled' => (!$isAdmin || $deviceStatus != 'active')
            ));
        }
        else {
            $actionMenu = '';
        }

        //Info link on name column
        $name = $modifiedOutputRow[0];
        $modifiedOutputRow[0] = $view->Strings->link($name,"/Devices/view/$deviceId");

        //Append action menu to last column
        $modifiedOutputRow[count($outputRow)-1] .= $actionMenu;

        //Add device status as a class to tr
        $modifiedOutputRow['DT_RowClass'] = "status status-$deviceStatus";

        return $modifiedOutputRow;
    });
