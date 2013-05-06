<?php

	$actionMenuItemsTemplate = array(
        array(
            'type' => 'modal',
            'text' => 'Edit Formation',
            'source' => '/Formations/edit/%__id__%.json',
        ),
        array(
            'type' => 'modal',
            'text' => 'Delete',
            'source' => '/Formations/delete/%__id__%.json'
        )
    );

    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) use($actionMenuItemsTemplate,$isAdmin){

        //Construct menu item from template
        $actionMenuItems = array();
        foreach($actionMenuItemsTemplate as $item){
            $item['source'] = str_replace('%__id__%',$rawRow['Formation']['id'],$item['source']);
            $item['disabled'] = !$isAdmin;
            $actionMenuItems[] = $item;
        }

        $actionMenu = $view->StringsActionMenu->actionMenu('Actions',$actionMenuItems,120);

		//Info link on name column
        $outputRow[0] = $view->Strings->modalLink($outputRow[0],"/Formations/view/" . $rawRow['Formation']['id'] . ".json");		

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
    });
