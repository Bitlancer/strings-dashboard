<?php

	if(!isset($emptyTableMessage))
		$emptyTableMessage = 'Add a member above';

    if(!isset($addInputPlaceholder))
        $addInputPlaceholder = 'name';

    if(!isset($tableColumnHeaders))
        $tableColumnHeaders = array('Name');

	$tableValues = array();
	foreach($memberData as $member){
		$tableRow = array();
		$tableRow[0] = $member['displayValue'];
		$tableRow[0] .= "<a class=\"action remove\" data-id=\"" . $member['id'] . "\">Remove</a>";
		$tableValues[] = $tableRow;
	}
?>
<style>
  form.association {
    padding:8px 8px 0px 8px;
  }
  form.association div.add {
    margin-bottom:5px;
  }
  form.association input.add {
    float:left;
    width:90%;
    margin-right:1%;
  }
  form.association a.cta.add {
    float:right;
    width:6%;
  }
</style>
<form class="association border-light-grey" data-src-add="<?php echo $addAssociationUri; ?>" data-src-remove="<?php echo $removeAssociationUri; ?>" data-callback="<?php $addAssociationCallback; ?>" >
    <ul id="notice">
    </ul>
    <div class="add">
      <input class="add autocomplete ui-autocomplete-input" type="text" placeholder="<?php echo $addInputPlaceholder; ?>" data-src="<?php echo $addAutocompleteUri; ?>" />
      <a class="cta primary small add">Add</a>
      <hr class="clear" />
    </div>
    <?php echo $this->StringsTable->cleanTable($tableColumnHeaders,$tableValues,$emptyTableMessage); ?>
<hr class="clear" />
</form>
