<?php

    $additionalClasses = array();

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
        'Deploy',
        "/Applications/view/$applicationId#script"
    );

    $actionMenu .= $this->Strings->modalLink(
        'DNS',
        "/Applications/manageDnsRecords/$applicationId",
        array(
            'width' => 500,
            'reloadOnClose' => $reloadOnClose
        )
    );

    $actionMenu .= $this->Strings->modalLink(
        'Formations',
         "/Applications/editFormations/$applicationId",
         array(
            'disabled' => $actionsDisabled,
            'width' => 360,
            'reloadOnClose' => $reloadOnClose
        )
    );

    $actionMenu .= $this->Strings->modalLink(
        'Unix Privileges',
        "/TeamInfrastructurePermissions/edit/Application/$applicationId",
        array(
            'disabled' => $actionsDisabled,
            'reloadOnClose' => $reloadOnClose
        )
    );

    /*
    $actionMenu .= $this->Strings->modalLink(
        'Rename',
        "/Applications/edit/$applicationId",
        array(
            'disabled' => $actionsDisabled,
            'reloadOnClose' => $reloadOnClose
        )
    );
    */

    $actionMenu .= $this->Strings->modalLink(
        'Delete',
        "/Applications/delete/$applicationId",
        array(
            'disabled' => $actionsDisabled,
            'reloadOnClose' => $reloadOnClose
        )
    );

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
