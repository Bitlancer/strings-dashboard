<?php

    require('action_menus.php');

	echo $this->DataTables->output($dataTable,
		function($view,$outputRow,$rawRow) use($enabledUserActionMenuItems,$disabledUserActionMenuItems,$isAdmin){

		$modifiedOutputRow = $outputRow;

		if($rawRow['User']['is_disabled'])
			$actionMenuItems = $disabledUserActionMenuItems;
		else
			$actionMenuItems = $enabledUserActionMenuItems;

		//Construct menu item from template
		$userActionMenuItems = array();
		foreach($actionMenuItems as $item){
			$item['source'] = str_replace('%__id__%',$rawRow['User']['id'],$item['source']);
			$item['disabled'] = !$isAdmin;
			$userActionMenuItems[] = $item;
		}

		$actionMenu = $view->StringsActionMenu->actionMenu('Actions',$userActionMenuItems,120);

		//If user is disabled, set disabled class on each column
		if($rawRow['User']['is_disabled']){
			for($x=0;$x<count($outputRow);$x++)
				$modifiedOutputRow[$x] = "<span class=\"disabled\">" . $outputRow[$x] . "</span>";
		}

		//Info link on name column
		$modifiedOutputRow[0] = $view->Strings->modalLink($modifiedOutputRow[0],"/Users/view/" . $rawRow['User']['id'] . ".json",false,$outputRow[0]);

		//Append action menu to last column
		$modifiedOutputRow[count($outputRow)-1] .= $actionMenu;

		return $modifiedOutputRow;

	});
