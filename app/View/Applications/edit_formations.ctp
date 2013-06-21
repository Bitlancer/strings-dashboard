<div id="edit-formations">
<form class="disabled">
 <ul id="notice">
 </ul>
 <fieldset>
    <legend>Formations</legend>
    <?php 
    $applicationId = $application['Application']['id'];
    echo $this->element('Associations/live',array(
      'tableId' => 'formations',
      'addAssociationUri' => '/Applications/addFormation/' . $applicationId,
      'removeAssociationUri' => '/Applications/removeFormation/' . $applicationId,
      'emptyTableMessage' => 'Add a Formation above',
      'inputAutocompleteUri' => '/Formations/searchByName'
    ));
  ?>
  </fieldset>
</form>
</div>
