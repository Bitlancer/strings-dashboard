<?php

	$editSudoRoleWidth = 500;

	$actionMenuItemsTemplate = array(
        array(
            'type' => 'modal',
            'text' => 'Edit Role',
            'source' => '/SudoRoles/edit/%__id__%.json',
			'width' => $editSudoRoleWidth
        ),
        array(
            'type' => 'modal',
            'text' => 'Delete',
            'source' => '/SudoRoles/delete/%__id__%.json'
        )
    );

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($actionMenuItemsTemplate,$editSudoRoleWidth,$isAdmin){

        //Construct menu item from template
        $actionMenuItems = array();
        foreach($actionMenuItemsTemplate as $item){
           	$item['source'] = str_replace('%__id__%',$rawRow['SudoRole']['id'],$item['source']);
            $item['disabled'] = !$isAdmin;
            $actionMenuItems[] = $item;
        }

        $actionMenu = $view->StringsActionMenu->actionMenu('Actions',$actionMenuItems,120);

		//Info link on name column
        $outputRow[0] = $view->Strings->modalLink($outputRow[0],"/sudoRoles/edit/" . $rawRow['SudoRole']['id'] . ".json",false,'Edit Role',$editSudoRoleWidth);

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
    });
