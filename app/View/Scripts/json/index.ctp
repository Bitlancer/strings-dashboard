<?php

echo $this->DataTables->render(
    function($view,$outputRow,$rawRow) use($isAdmin){

        $actionMenu = $view->element('../Scripts/elements/action_menu',array(
            'scriptId' => $rawRow['Script']['id'],
            'actionsDisabled' => !$isAdmin
        ));

        //Info link on name column
        $outputRow[0] = $view->Strings->modalLink(
            $outputRow[0],
            "/Scripts/edit/" . $rawRow['Script']['id'],
            array(
                'title' => 'Edit script',
                'width' => '500'
            )
        );

        //Append action menu to last column
        $outputRow[count($outputRow)-1] .= $actionMenu;

        return $outputRow;
});
