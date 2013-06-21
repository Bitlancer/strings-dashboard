<?php

/*
@param $tableId string Table element id
@param $tableDataSrc string Ajax data source
@param $addAssociationUri URL called to add an association
@param $removeAssociationUri URL called to remove an association
@param $inputAutocompleteUri URL called to retrieve auto complete results
@param $emptyTableMessage Message displayed if the table is empty
*/

?>
<style>
  .association #add {
    margin-bottom:6px;
  }
  .association #add input {
    width:82%;
    margin:0;
  }
  .association #add .cta {
    width:12%;
    margin:0;
  }
</style>
<div class="association" data-src-add="<?php echo $addAssociationUri; ?>" data-src-remove="<?php echo $removeAssociationUri; ?>">
  <div id="add">
    <input type="text" class="autocomplete" placeholder="name" data-src="<?php echo $inputAutocompleteUri; ?>" />
    <a class="cta primary small add">Add</a>
  </div>
  <?php
    echo $this->element('Datatables/basic',array(
        'tableId' => $tableId,
        'columnHeadings' => isset($columnHeadings) ? $columnHeadings : array('Name'), 
        'dataSrc' => isset($tableDataSrc) ? $tableDataSrc : $this->here . ".json",
        'emptyTableMsg' => $emptyTableMessage,
        'processing' => true,
        'paginate' => false
    ));
    ?>
</div>
