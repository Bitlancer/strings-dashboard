<?php

	$actionMenuItems = array(
        array(
            'text' => 'Edit Application',
            'action' => 'edit'
        ),
		array(
			'text' => 'Edit Formations',
			'action' => 'edit_formations'
		),
		array(
            'text' => 'Delete',
            'action' => 'delete'
        )
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
