<?php

	echo $this->DataTables->render(
		function($view,$outputRow,$rawRow) use($isAdmin){

		$modifiedOutputRow = $outputRow;

        $actionMenu = $view->element('../Users/_action_menu',array(
            'userId' => $rawRow['User']['id'],
            'userEnabled' => !$rawRow['User']['is_disabled'],
            'actionsDisabled' => !$isAdmin
        ));

		//If user is disabled, set disabled class on each column
		if($rawRow['User']['is_disabled']){
			for($x=0;$x<count($outputRow);$x++)
				$modifiedOutputRow[$x] = "<span class=\"disabled\">" . $outputRow[$x] . "</span>";
		}

		//Info link on name column
		$modifiedOutputRow[0] = $view->Strings->link($modifiedOutputRow[0],"/Users/view/" . $rawRow['User']['id']);

		//Append action menu to last column
		$modifiedOutputRow[count($outputRow)-1] .= $actionMenu;

		return $modifiedOutputRow;

	});
