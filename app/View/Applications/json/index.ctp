<?php

	$actionMenuItems = array(
        array(
            'text' => '',
            'action' => ''
        ),
    );

    echo $this->DataTables->output($dataTable,function($view,$outputRow,$rawRow) use($actionMenuItems,$isAdmin){

        $actionMenu = $view->Strings->createActionMenu(120,'Actions');
        foreach($actionMenuItems as $item){
            $source = "/Applications/" . $item['action'] . "/" . $rawRow['Application']['id'] . ".json";
            $actionMenu .= $view->Strings->actionMenuItem($item['text'],$source,$isAdmin);
        }
        $actionMenu .= $view->Strings->closeActionMenu();

        //Append action menu
        $outputRow[0] .= $actionMenu;

        return $outputRow;

    });
