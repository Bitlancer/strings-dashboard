<?php

    if(!isset($title))
        $title = 'Actions';

    if(!isset($align))
        $align = 'right';

    if(!isset($width))
        $width = 120;

    if(!isset($actionsDisabled))
        $actionsDisabled = false;

    $actionMenu = $this->StringsActionMenu->create($title,$width,$align);

    $actionMenu .= $this->Strings->link('Manage Nodes',"/Devices/manageNodes/$deviceId",$actionsDisabled);
    $actionMenu .= $this->Strings->link('Configure',"/Devices/configure/$deviceId",$actionsDisabled);
    $actionMenu .= $this->Strings->oldModalLink('Delete',"/Formations/deleteDevice/$deviceId",$actionsDisabled,"Delete Device");

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
