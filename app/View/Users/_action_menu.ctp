<?php

    if(!isset($align))
        $align = 'right';
    
    if(!isset($width))
        $width = 120;
        
    if(!isset($actionsDisabled))
        $actionsDisabled = false;
        
    if(!isset($userEnabled))
        $userEnabled = true;

    $actionMenu = $this->StringsActionMenu->create('Actions',120,$align);
    
    if($userEnabled){
        $actionMenu .= $this->Strings->modalLink('Edit User',"/Users/edit/$userId.json",$actionsDisabled);
        $actionMenu .= $this->Strings->modalLink('Reset Password',"/Users/edit/$userId.json",$actionsDisabled);
        $actionMenu .= $this->Strings->modalLink('Disable User',"/Users/disable/$userId.json",$actionsDisabled);
    }
    else {
        $actionMenu .= $this->Strings->modalLink('Re-enable User',"/Users/enable/$userId.json",$actionsDisabled);
    }
    
    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
