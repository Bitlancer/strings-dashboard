<?php

    if(!isset($title))
        $title = 'Actions';

    if(!isset($align))
        $align = 'right';
    
    if(!isset($width))
        $width = 120;
        
    if(!isset($actionsDisabled))
        $actionsDisabled = false;

    if(!isset($reloadOnClose))
        $reloadOnClose = false;

    $actionMenu = $this->StringsActionMenu->create($title,$width,$align);

    //Menu items
    $actionMenu .= $this->Strings->link(
        'Run',
        "/Scripts/run/$scriptId"
    );

    $actionMenu .= $this->Strings->modalLink(
        'Edit',
        "/Scripts/edit/$scriptId",
        array(
            'title' => 'Edit script',
            'width' => 500,
            'disabled' => $actionsDisabled,
            'reloadOnClose' => $reloadOnClose
        )
    );

    $actionMenu .= $this->Strings->modalLink(
        'Delete',
        "/Scripts/delete/$scriptId",
        array(
            'title' => 'Delete script',
            'disabled' => $actionsDisabled,
            'reloadOnClose' => $reloadOnClose
        )
    );

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
