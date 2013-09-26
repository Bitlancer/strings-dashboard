<?php

    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) use($isAdmin){

        $actionMenu = $view->element("../SudoRoles/_action_menu",array(
            'sudoRoleId' => $rawRow['SudoRole']['id'],
            'actionsDisabled' => !$isAdmin
        ));

		//Info link on name column
        $outputRow[0] = $view->Strings->link($outputRow[0],"/sudoRoles/view/" . $rawRow['SudoRole']['id']);

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
    });
