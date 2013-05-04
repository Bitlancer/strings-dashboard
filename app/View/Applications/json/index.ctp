<?php

	$actionMenuItemsTemplate = array(
        array(
            'type' => 'modal',
            'text' => 'Edit Application',
            'source' => '/Applications/edit/%__id__%.json',
        ),
        array(
            'type' => 'modal',
            'text' => 'Edit Formations',
            'source' => '/Application/edit_formations/%__id__%.json'
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
            $item['source'] = str_replace('%__id__%',$rawRow['Application']['id'],$item['source']);
            $item['enabled'] = $isAdmin;
            $userActionMenuItems[] = $item;
        }

        $actionMenu = $view->StringsActionMenu->actionMenu('Actions',$userActionMenuItems,120);

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;

    });
