<?php

	$memberData = array();
	foreach($members as $member){

		$member['id'] = $member['Formation']['id'];
		$member['displayValue'] = $member['Formation']['name'];

		$memberData[] = $member;
	}

	echo $this->element('association',array(
		'memberData' => $memberData,
		'addAssociationTitle' => 'Add Formation',
		'removeAssociationTitle' => 'Existing Formations',
		'addAssociationUri' => "/Applications/addFormation/$id.json",
		'removeAssociationUri' => "/Applications/removeFormation/$id.json"
	));
