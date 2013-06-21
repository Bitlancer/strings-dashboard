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

    $actionMenu .= $this->Strings->modalLink('Edit Device',"/Devices/edit/$deviceId",true);
    $actionMenu .= $this->Strings->modalLink('Resize Device',"/Devices/resize/$deviceId",$actionsDisabled);
    $actionMenu .= $this->Strings->modalLink('Delete',"/Formations/deleteDevice/$formationId",$actionsDisabled);

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
