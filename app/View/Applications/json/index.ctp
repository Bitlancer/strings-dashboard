<?php

    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) use($isAdmin){

        $actionMenu = $view->element('../Applications/_elements/action_menu',array(
            'applicationId' => $rawRow['Application']['id'],
            'actionsDisabled' => !$isAdmin
        ));

		//Info link on name column
        $outputRow[0] = $view->Strings->link($outputRow[0],"/Applications/view/" . $rawRow['Application']['id']);

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
    });
