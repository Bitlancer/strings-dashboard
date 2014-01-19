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

    $actionMenu .= $this->Strings->link('Configure',"/Devices/configure/$deviceId",$actionsDisabled);
    $actionMenu .= $this->Strings->oldModalLink('Unix Privileges',"/TeamInfrastructurePermissions/edit/Formation/$deviceId",$actionsDisabled);
    $actionMenu .= $this->Strings->link('Reboot',"/Devices/reboot/$deviceId",$actionsDisabled);
    $actionMenu .= $this->Strings->oldModalLink('Resize',"/Devices/resize/$deviceId",$actionsDisabled,"Resize Device", 500);
    $actionMenu .= $this->Strings->oldModalLink('Delete',"/Formations/deleteDevice/$deviceId",$actionsDisabled,"Delete Device");

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
