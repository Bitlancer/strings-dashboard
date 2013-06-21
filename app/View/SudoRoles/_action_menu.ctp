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

    $actionMenu .= $this->Strings->modalLink('Edit',"/SudoRoles/edit/$sudoRoleId",$actionsDisabled);
    $actionMenu .= $this->Strings->modalLink('Delete',"/SudoRoles/delete/$sudoRoleId",$actionsDisabled);

    $actionMenu .= $this->StringsActionMenu->close();

    echo $actionMenu;

?>
