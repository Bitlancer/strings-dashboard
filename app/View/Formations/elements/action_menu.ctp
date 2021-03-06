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

    $actionMenu .= $this->Strings->link('Devices',"/Formations/view/$formationId#devices",$actionsDisabled);
    $actionMenu .= $this->Strings->oldModalLink('Unix Privileges',"/TeamInfrastructurePermissions/edit/Formation/$formationId",$actionsDisabled);
    //$actionMenu .= $this->Strings->oldModalLink('Rename',"/Formations/edit/$formationId",$actionsDisabled);
    $actionMenu .= $this->Strings->oldModalLink('Delete',"/Formations/delete/$formationId",$actionsDisabled);

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
