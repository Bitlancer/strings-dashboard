<?php

	echo $this->DataTables->output($dataTable,
		function($view,$outputRow,$rawRow) use($isAdmin){

		$modifiedOutputRow = $outputRow;

        $actionMenu = $view->element('../Teams/_action_menu',array(
            'teamId' => $rawRow['Team']['id'],
            'teamEnabled' => !$rawRow['Team']['is_disabled'],
            'actionsDisabled' => !$isAdmin
        ));

		//If team is disabled, set disabled class on each column
		if($rawRow['Team']['is_disabled']){
			for($x=0;$x<count($outputRow);$x++)
				$modifiedOutputRow[$x] = "<span class=\"disabled\">" . $outputRow[$x] . "</span>";
		}

		//Info link on name column
		$modifiedOutputRow[0] = $view->Strings->modalLink($modifiedOutputRow[0],"/Teams/view/" . $rawRow['Team']['id'] . ".json",false,$outputRow[0]);

		//Append action menu to last column
		$modifiedOutputRow[count($outputRow)-1] .= $actionMenu;

		return $modifiedOutputRow;

	});
