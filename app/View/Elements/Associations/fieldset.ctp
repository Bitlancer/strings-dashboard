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
}
fieldset.association div#add .cta {
  width:12%;
  vertical-align:baseline;
}
fieldset.association table {
  margin: 10px 0;
}
</style>
<fieldset id="<?php echo $fieldsetId; ?>" class="association" data-field-name="<?php echo $memberFieldName; ?>" data-empty-table-msg="<?php echo $emptyTableMessage; ?>">
  <legend><?php echo $fieldsetTitle; ?></legend>
  <div id="add">
    <input class="autocomplete disable-autosubmit" type="text" placeholder="name" data-src="<?php echo $memberAutocompleteSrc; ?>" />
    <a class="cta primary small add">Add</a>
  </div>
  <?php echo $this->StringsTable->cleanTable(array(''),$tableValues,$emptyTableMessage); ?>
</fieldset>
<script>
  $("fieldset.association input[type='text']").keypress(function(e){
    if(e.which == 13){
      $(this).closest("fieldset").find(".cta.add").click();
    }
  });
  $("fieldset.association .cta.add").live('click', function(e){
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
  $('fieldset.association .action.remove').live('click', function(e){
    var src = $(this);
    var emptyTableMessage = src.closest('fieldset').attr('data-empty-table-msg');
    var tbody = src.closest('tbody');
    src.closest('tr').remove();
    if(tbody.find('tr').length == 0){
      tbody.append("<tr><td class='blank'>" + emptyTableMessage + "</td></tr>");
    }
  });
</script>
