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

    if(!isset($inputPlaceholder))
        $inputPlaceholder = 'name';

	$tableValues = array();
	foreach($memberData as $member){
        $displayName = false;
        if(is_array($member))
            $displayName = $member['name'];
        else
            $displayName = $member;
		$tableRow = array();
		$tableRow[0] = $displayName;
        $tableRow[0] .= "<input type=\"hidden\" name=\"" . $memberFieldName . "\" value=\"" . $displayName . "\" />";
		$tableRow[0] .= "<a class=\"action remove\">Remove</a>";
		$tableValues[] = $tableRow;
	}
?>
<style>
fieldset.association-old div#add input {
  width:78%;
}
fieldset.association-old div#add .cta {
  width:12%;
  vertical-align:baseline;
}
fieldset.association-old table {
  margin: 10px 0;
}
</style>
<fieldset id="<?php echo $fieldsetId; ?>" class="association-old" data-field-name="<?php echo $memberFieldName; ?>" data-empty-table-msg="<?php echo $emptyTableMessage; ?>">
  <legend><?php echo $fieldsetTitle; ?></legend>
  <div id="add">
    <input class="autocomplete disable-autosubmit" type="text" placeholder="<?php echo $inputPlaceholder; ?>" data-src="<?php echo $memberAutocompleteSrc; ?>" />
    <a class="cta primary small add">Add</a>
  </div>
  <?php echo $this->StringsTable->cleanTable(array('Name'),$tableValues,$emptyTableMessage); ?>
</fieldset>
<script>
  $("fieldset.association-old input[type='text']").keypress(function(e){
    if(e.which == 13){
      e.preventDefault();
      $(this).closest("fieldset").find(".cta.add").click();
    }
  });
  $("fieldset.association-old .cta.add").live('click', function(e){
    e.preventDefault();
    var src = $(this);
    var fieldset = src.closest("fieldset");
    var input = fieldset.find("input[type='text']");
    var name = input.val();
    var tbody = fieldset.find("tbody");
    if(name.length > 0 && tbody.find("input[value='" + name + "']").length == 0){
      $(tbody).find("td.blank").parent("tr").remove();
      var element = "<tr><td>";
      element += name;
      element += "<input type='hidden' name='" + fieldset.attr('data-field-name') + "' value='" + name + "' />";
      element += "<a class='action remove'>Remove</a>";
      tbody.append(element);
    }
    input.val("");
  });
  $('fieldset.association-old .action.remove').live('click', function(e){
    var src = $(this);
    var emptyTableMessage = src.closest('fieldset').attr('data-empty-table-msg');
    var tbody = src.closest('tbody');
    src.closest('tr').remove();
    if(tbody.find('tr').length == 0){
      tbody.append("<tr><td class='blank'>" + emptyTableMessage + "</td></tr>");
    }
  });
</script>
