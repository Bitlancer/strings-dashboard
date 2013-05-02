<?php

	$actionMenuItems = array(
        array(
            'text' => 'Edit User',
            'action' => 'edit'
        ),
        array(
            'text' => 'Reset Password',
            'action' => 'reset_password'
        ),
        array(
            'text' => 'Disable User',
            'action' => 'disable'
        )
    );

	echo $this->DataTables->output($dataTable,function($view,$outputRow,$rawRow) use($actionMenuItems,$isAdmin){

		$actionMenu = $view->Strings->createActionMenu(120,'Actions');
        foreach($actionMenuItems as $item){
            $source = "/Users/" . $item['action'] . "/" . $rawRow['User']['id'] . ".json";
            $actionMenu .= $view->Strings->actionMenuItem($item['text'],$source,$isAdmin);
        }
        $actionMenu .= $view->Strings->closeActionMenu();

		//Append action menu
		$outputRow[0] .= $actionMenu;

		return $outputRow;

	});
