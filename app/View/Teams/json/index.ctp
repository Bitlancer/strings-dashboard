<?php

	$enabledTeamActionMenuItems = array(
        array(
			'type' => 'modal',
            'text' => 'Edit Team',
			'source' => '/Teams/edit/%__id__%.json',
        ),
        array(
			'type' => 'modal',
            'text' => 'Disable Team',
			'source' => '/Teams/disable/%__id__%.json'
        )
    );

	$disabledTeamActionMenuItems = array(
		array(
			'type' => 'modal',
			'text' => 'Re-enable Team',
			'source' => '/Teams/enable/%__id__%.json'
		)
	);

	echo $this->DataTables->output($dataTable,
		function($view,$outputRow,$rawRow) use($enabledTeamActionMenuItems,$disabledTeamActionMenuItems,$isAdmin){

		$modifiedOutputRow = $outputRow;

		if($rawRow['Team']['is_disabled'])
			$actionMenuItemsTemplate = $disabledTeamActionMenuItems;
		else
			$actionMenuItemsTemplate = $enabledTeamActionMenuItems;

		//Construct menu item from template
		$actionMenuItems = array();
		foreach($actionMenuItemsTemplate as $item){
			$item['source'] = str_replace('%__id__%',$rawRow['Team']['id'],$item['source']);
			$item['disabled'] = !$isAdmin;
			$actionMenuItems[] = $item;
		}

		$actionMenu = $view->StringsActionMenu->actionMenu('Actions',$actionMenuItems,120);

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
