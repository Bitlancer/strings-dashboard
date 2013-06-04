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
    
    $actionMenu .= $this->Strings->modalLink('Edit Permissions',"/Applications/editTeamPermissions/$applicationId/$teamId.json",$actionsDisabled);
    $actionMenu .= $this->Strings->modalLink('Remove',"/Applications/removeTeam/$applicationId/$teamId.json",$actionsDisabled);
    
    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>