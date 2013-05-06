<div id="view-formation">
  <?php 
  echo $this->StringsTable->infoTable(array(
		'Name' => $formation['Formation']['name'],
		'Created' => date(DEFAULT_DATE_FORMAT,strtotime($formation['Formation']['created']))
  ));
  ?>
</div> <!-- /view-formation -->
