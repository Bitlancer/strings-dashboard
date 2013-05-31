<?php

	$actionMenuItemsTemplate = array(
        array(
            'type' => 'modal',
            'text' => 'Edit Permissions',
            'source' => '/Applications/edit/%__id__%.json',
        ),
        array(
            'type' => 'modal',
            'text' => 'Delete',
            'source' => '/Applications/delete/%__id__%.json'
        )
    );

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($actionMenuItemsTemplate,$isAdmin){

        //Construct menu item from template
        $actionMenuItems = array();
        foreach($actionMenuItemsTemplate as $item){
			if($item['type'] == 'modal')
            	$item['source'] = str_replace('%__id__%',$rawRow['Team']['id'],$item['source']);
			else
				$item['destination'] = str_replace('%__id__%',$rawRow['Team']['id'],$item['destination']);
            $item['disabled'] = !$isAdmin;
            $actionMenuItems[] = $item;
        }

        $actionMenu = $view->StringsActionMenu->actionMenu('Actions',$actionMenuItems,120);

		//Info link on name column
        $outputRow[0] = $view->Strings->modalLink($outputRow[0],"/Applications/view/" . $rawRow['Team']['id'] . ".json");		

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
    });
