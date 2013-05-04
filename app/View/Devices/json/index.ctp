<?php

	$actionMenuItemsTemplate = array(
        array(
            'type' => 'modal',
            'text' => 'Detailed View',
            'source' => '/Device/detailed_view/%__id__%.json',
        ),
        array(
            'type' => 'link',
            'text' => 'View Formation',
            'source' => '/Formations/view/%__formation_id__%.json'
        )
    );

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($actionMenuItemsTemplate,$isAdmin){

        //Construct menu item from template
        $actionMenuItems = array();
        foreach($actionMenuItemsTemplate as $item){
            $item['source'] = str_replace('%__id__%',$rawRow['User']['id'],$item['source']);
			$item['source'] = str_replace('%__formation_id__%',$rawRow['Formation']['id'],$items['source']);
            $item['enabled'] = $isAdmin;
            $userActionMenuItems[] = $item;
        }

        $actionMenu = $view->StringsActionMenu->actionMenu('Actions',$actionMenuItems,120);

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;

    });
