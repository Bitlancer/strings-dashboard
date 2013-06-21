<?php

    $memberData = array();
    foreach($members as $member){

        $member['id'] = $member['User']['id'];
        $member['displayValue'] = $member['User']['full_name'] . " (" . $member['User']['name'] . ")";

        $memberData[] = $member;
    }

    echo $this->element('association',array(
        'memberData' => $memberData,
        'addAssociationTitle' => 'Add Member',
        'removeAssociationTitle' => 'Existing Members',
        'addAssociationUri' => "/Teams/addUser/$id.json",
        'removeAssociationUri' => "/Teams/removeUser/$id.json"
    ));
