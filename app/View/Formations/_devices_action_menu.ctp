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

    $actionMenu .= $this->Strings->modalLink('Edit',"/Devices/edit/$deviceId",true,"Edit Device");
    $actionMenu .= $this->Strings->modalLink('Resize',"/Devices/resize/$deviceId",$actionsDisabled,"Resize Device");
    $actionMenu .= $this->Strings->modalLink('Delete',"/Formations/deleteDevice/$deviceId",$actionsDisabled,"Delete Device");

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
