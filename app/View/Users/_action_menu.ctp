<?php

    if(!isset($title))
        $title = 'Actions';

    if(!isset($align))
        $align = 'right';
    
    if(!isset($width))
        $width = 120;
        
    if(!isset($actionsDisabled))
        $actionsDisabled = false;
        
    if(!isset($userEnabled))
        $userEnabled = true;

    $actionMenu = $this->StringsActionMenu->create($title,$width,$align);
    
    if($userEnabled){
        $actionMenu .= $this->Strings->oldModalLink('Edit User',"/Users/edit/$userId",$actionsDisabled);
        $actionMenu .= $this->Strings->link('SSH Keys',"/Users/sshKeys/$userId",$actionsDisabled);
        $actionMenu .= $this->Strings->oldModalLink('Reset Password',"/Users/changePassword/$userId",$actionsDisabled);
        $actionMenu .= $this->Strings->oldModalLink('Disable User',"/Users/disable/$userId",$actionsDisabled);
    }
    else {
        $actionMenu .= $this->Strings->oldModalLink('Re-enable User',"/Users/enable/$userId",$actionsDisabled);
    }
    
    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
