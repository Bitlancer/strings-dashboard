<?php

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($isAdmin){

        $modifiedOutputRow = $outputRow;

        $actionMenu = $view->element('../Formations/_devices_action_menu',array(
            'deviceId' => $rawRow['Device']['id'],
            'formationId' => $rawRow['Device']['formation_id'],
            'actionsDisabled' => (!$isAdmin || $rawRow['Device']['status'] !== 'active')
        ));

        //Info link on name column
        $modifiedOutputRow[0] = $view->Strings->link($modifiedOutputRow[0],"/Devices/view/" . $rawRow['Device']['id']);

        //Append action menu to last column
        $modifiedOutputRow[count($outputRow)-1] .= $actionMenu;

        return $modifiedOutputRow;
    });
