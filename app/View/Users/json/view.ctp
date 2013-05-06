<div id="view-user">
  <!--
  <h2>
  <?php 
	echo $this->StringsActionMenu->create('User Actions');
    echo $this->StringsActionMenu->modalItem('Edit User','/Users/edit/' . $user['User']['id'] . '.json',true);
    echo $this->StringsActionMenu->close();
  ?>
  </h2>
  -->
  <?php 
  echo $this->StringsTable->infoTable(array(
		'Status' => $user['User']['is_disabled'] ? 'Disabled' : 'Enabled',
		'Username' => $user['User']['name'],
		'Name' => $user['User']['full_name'],
		'Email' => $user['User']['email'],
		'Created' => $this->Time->format(DEFAULT_DATE_FORMAT,$user['User']['created'])
  ));
  ?>
</div> <!-- /view-user -->
