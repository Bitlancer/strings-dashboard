<?php

    if(!isset($memberData))
        $memberData = array();

    if(!isset($memberFieldName))
        $memberFieldName = 'data[members][]';

    if(!isset($fieldsetId))
        $fieldsetId = 'members';

    if(!isset($fieldsetTitle))
        $fieldsetTitle = 'Members';

	if(!isset($emptyTableMessage))
		$emptyTableMessage = 'Add a member above';

	$tableValues = array();
	foreach($memberData as $member){
		$tableRow = array();
		$tableRow[0] = $member['name'];
        $tableRow[0] .= "<input type=\"hidden\" name=\"" . $memberFieldName . "\" value=\"" . $member['name'] . "\" />";
		$tableRow[0] .= "<a class=\"action remove\">Remove</a>";
		$tableValues[] = $tableRow;
	}
?>
<style>
fieldset.association div#add input {
  width:82%;
  margin-right: 1%;
}
fieldset.association table {
  margin: 10px 0;
}
</style>
<fieldset id="<?php echo $fieldsetId; ?>" class="association" data-field-name="<?php echo $memberFieldName; ?>">
  <legend><?php echo $fieldsetTitle; ?></legend>
  <div id="add">
    <input class="autocomplete" type="text" placeholder="name" data-src="<?php echo $memberAutocompleteSrc; ?>" />
    <a class="cta primary small add">Add</a>
  </div>
  <?php echo $this->StringsTable->cleanTable(array(''),$tableValues,$emptyTableMessage); ?>
</fieldset>
