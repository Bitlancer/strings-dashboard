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
    
    $actionMenu .= $this->Strings->modalLink('Edit Application',"/Applications/edit/$applicationId.json",$actionsDisabled);
    $actionMenu .= $this->Strings->modalLink('Edit Formations',"/Applications/editFormations/$applicationId.json",$actionsDisabled);
    $actionMenu .= $this->Strings->link('Edit Permissions',"/Applications/editPermissions/$applicationId",$actionsDisabled);
    $actionMenu .= $this->Strings->modalLink('Delete',"/Applications/delete/$applicationId.json",$actionsDisabled);
    
    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
