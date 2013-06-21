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

    if(isset($reload) && $reload)
        $additionalClasses[] = 'reload'; 
        
    $actionMenu = $this->StringsActionMenu->create($title,$width,$align);
    
    $actionMenu .= $this->Strings->modalLink('Deploy',"/Applications/deploy/$applicationId",true);
    $actionMenu .= $this->Strings->modalLink('DNS',"/Applications/dns/$applicationId",true);
    $actionMenu .= $this->Strings->modalLink('Formations',"/Applications/editFormations/$applicationId",$actionsDisabled,false,360,$additionalClasses);
    $actionMenu .= $this->Strings->modalLink('Unix Privileges',"/TeamInfrastructurePermissions/edit/Application/$applicationId",$actionsDisabled);
    $actionMenu .= $this->Strings->modalLink('Rename',"/Applications/edit/$applicationId",$actionsDisabled);
    $actionMenu .= $this->Strings->modalLink('Delete',"/Applications/delete/$applicationId",$actionsDisabled);

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
