<?php
	$controller = strtolower($this->params['controller']);

	$dashboardControllers = array('dashboard');
	$devicesControllers = array('devices');
	$formationsControllers = array('formations');
	$applicationsControllers = array('applications');
	$usersControllers = array('users','teams','sudoroles');
?>
<!--
<span id="dashboard" class="<?php if(in_array($controller,$dashboardControllers)) { echo 'active'; } ?>" >
  <a href="/">Dashboard</a>
</span>
-->
<span id="devices" class="<?php if(in_array($controller,$devicesControllers)) { echo 'active'; } ?>">
  <a href="/devices">Devices</a>
</span>
<span id="formations" class="<?php if(in_array($controller,$formationsControllers)) { echo 'active'; } ?>">
  <a href="/formations">Formations</a>
</span>
<span id="applications" class="<?php if(in_array($controller,$applicationsControllers)) { echo 'active'; } ?>">
  <a href="/applications">Applications</a>
</span>
<span id="user-management" class="<?php if(in_array($controller,$usersControllers)) { echo 'active'; } ?>">
  <a href="/users" rel="noreferrer">User Management</a>
  <span>
	<a href="/users" class="<?php if($controller == 'users') { echo 'active'; } ?>" >Users</a>
    <a href="/teams" class="<?php if($controller == 'teams') { echo 'active'; } ?>" >Teams</a>
    <a href="/sudo" class="<?php if($controller == 'sudoroles') { echo 'active'; } ?>" >Sudo</a>
  </span>
</span>
