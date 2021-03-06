<?php

    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) use($isAdmin){

        $actionMenu = $view->element('../Applications/elements/action_menu',array(
            'applicationId' => $rawRow['Application']['id'],
            'actionsDisabled' => !$isAdmin
        ));

		//Info link on name column
        $name = $outputRow[0];
        $outputRow[0] = $view->Strings->link($name,"/Applications/view/" . $rawRow['Application']['id']);

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
    });
