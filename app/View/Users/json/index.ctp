<?php

	$enabledUserActionMenuItems = array(
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

	$disabledUserActionMenuItems = array(
		array(
			'text' => 'Re-enable User',
			'action' => 'enable'
		)
	);

	echo $this->DataTables->output($dataTable,function($view,$outputRow,$rawRow) use($enabledUserActionMenuItems,$disabledUserActionMenuItems,$isAdmin){

		if($rawRow['User']['is_disabled'])
			$actionMenuItems = $disabledUserActionMenuItems;
		else
			$actionMenuItems = $enabledUserActionMenuItems;

		$actionMenu = $view->Strings->createActionMenu(120,'Actions');
        foreach($actionMenuItems as $item){
            $source = "/Users/" . $item['action'] . "/" . $rawRow['User']['id'] . ".json";
            $actionMenu .= $view->Strings->actionMenuItem($item['text'],$source,$isAdmin);
        }
        $actionMenu .= $view->Strings->closeActionMenu();

		//If user is disabled, set disabled class on each column
		if($rawRow['User']['is_disabled']){
			for($x=0;$x<count($outputRow);$x++)
				$outputRow[$x] = "<span class=\"disabled\">" . $outputRow[$x] . "</span>";
		}

		//Append action menu to last column
		$outputRow[count($outputRow)-1] .= $actionMenu;

		return $outputRow;

	});
