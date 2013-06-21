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
<form class="association border-light-grey" 
        data-src-add="<?php echo $addAssociationUri; ?>" 
        data-src-remove="<?php echo $removeAssociationUri; ?>" 
        data-callback="<?php $addAssociationCallback; ?>" >
    <ul id="notice">
    </ul>
    <div class="add">
      <input class="add autocomplete ui-autocomplete-input disable-autosubmit" type="text" placeholder="<?php echo $addInputPlaceholder; ?>" data-src="<?php echo $addAutocompleteUri; ?>" />
      <a class="cta primary small add">Add</a>
      <hr class="clear" />
    </div>
    <?php echo $this->StringsTable->cleanTable($tableColumnHeaders,$tableValues,$emptyTableMessage); ?>
<hr class="clear" />
</form>
<script>
  $("form.association input[type='text']").keypress(function(e){
    if(e.which == 13){
      $(this).closest("form").find(".cta.add").click();
    }
  });
  $('form.association .cta.add').live('click',function(e){
    e.preventDefault();
    var src = $(this);
    var form = src.closest('form');
    var input = form.find('input');
    var name = input.val();
    var notice = form.find("#notice");
    notice.empty();
    $.ajax({
      type: "post",
      url: form.attr('data-src-add'),
      data: {
        "name": name
      },
      success: function(data, textStatus){
        if(!data.isError){
          var tbody = form.find("table tbody");
          $(tbody).find('td.blank').parent('tr').remove();
          var element = "<tr><td>";
          element += name;
          element += "<a class='action remove' data-id='" + data.id + "'>Remove</a>";
          element += "</td></tr>";
          $(tbody).append(element);
          input.val("");
        }
        else {
          notice.append("<li class='error'>" + data.message + "</li>");
        }
      }
    })
    .error(function(jqXHR,textStatus){
      console.log(textStatus);
    });
  });
  $('form.association .action.remove').live('click',function(e){
    e.preventDefault();
    var src = $(this);
    var form = src.closest('form');
    var notice = form.find("#notice");
    notice.empty();
    $.ajax({
      type: "post",
      url: form.attr('data-src-remove'),
      data: {
        "id": src.attr('data-id')
      },
      success: function(data, textStatus){
        var tbody = src.closest('tbody');
        src.closest('tr').remove();
        if(tbody.find('tr').length == 0){
          tbody.append("<tr><td class='blank'>Add a member above</td></tr>");
        }
      }
    })
    .error(function(jqXHR,textStatus){
      console.log(textStatus);
    });
  });
</script>
