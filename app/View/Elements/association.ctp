<?php

	if(!isset($emptyTableMessage))
		$emptyTableMessage = 'Add a member above';

	$tableValues = array();
	foreach($memberData as $member){
		$tableRow = array();
		$tableRow[0] = $member['displayValue'];
		$tableRow[0] .= "<a class=\"action remove\" data-id=\"" . $member['id'] . "\">Remove</a>";
		$tableValues[] = $tableRow;
	}
?>
<style>
fieldset#add input {
  margin-right: 5px;
  width:88%;
}
fieldset table {
  margin: 10px 0;
}
</style>
<form class="association" data-src-add="<?php echo $addAssociationUri; ?>" data-src-remove="<?php echo $removeAssociationUri; ?>" >
  <ul id="notice">
  </ul>
  <fieldset id="add">
    <legend><?php echo $addAssociationTitle; ?></legend>
    <input type="text" placeholder="name" />
    <a class="cta primary small add">Add</a>
  </fieldset>
  <fieldset id="remove">
    <legend><?php echo $removeAssociationTitle; ?></legend>
    <?php echo $this->StringsTable->cleanTable(array(''),$tableValues,$emptyTableMessage); ?>
  </fieldset>
</div>
</form>
