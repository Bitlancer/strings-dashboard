<?php

    if(!isset($title))
        $title = 'Actions';

    if(!isset($align))
        $align = 'right';

    if(!isset($width))
        $width = 120;

    if(!isset($actionsDisabled))
        $actionsDisabled = false;

    if(!isset($teamEnabled))
        $teamEnabled = true;

    $actionMenu = $this->StringsActionMenu->create($title,$width,$align);

    if($teamEnabled){
        $actionMenu .= $this->Strings->oldModalLink('Edit Team',"/Teams/edit/$teamId",$actionsDisabled);
        $actionMenu .= $this->Strings->oldModalLink('Disable Team',"/Teams/disable/$teamId",$actionsDisabled);
    }
    else {
        $actionMenu .= $this->Strings->oldModalLink('Re-enable Team',"/Teams/enable/$teamId",$actionsDisabled);
    }

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
