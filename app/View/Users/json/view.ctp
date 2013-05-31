<div id="view-user">
  <h2>
  <?php 
    echo $this->element('../Users/_action_menu',array(
        'align' => 'left',
        'userId' => $user['User']['id'],
        'userEnabled' => !$user['User']['is_disabled'],
        'actionsDisabled' => !$isAdmin
    ));  
  ?>
  </h2>
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
