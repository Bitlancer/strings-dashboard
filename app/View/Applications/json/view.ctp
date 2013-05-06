<div id="view-application">
  <?php 
  echo $this->StringsTable->infoTable(array(
		'Name' => $application['Application']['name'],
		'Created' => date(DEFAULT_DATE_FORMAT,strtotime($application['Application']['created']))
  ));
  ?>
</div> <!-- /view-application -->

