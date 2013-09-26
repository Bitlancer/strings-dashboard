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

    $actionMenu = $this->StringsActionMenu->create($title,$width,$align);
    
    $actionMenu .= $this->Strings->oldModalLink('Unix Privileges',"/TeamInfrastructurePermissions/edit/Role/$roleId",$actionsDisabled);

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
