<?php echo $this->Session->flash(); ?>
<form method="post" action="/users/create">
	<input type="text" name="data[User][name]" />
	<input type="text" name="data[User][password]" />
	<input type="text" name="data[User][first_name]" />
	<input type="text" name="data[User][last_name]" />
	<input type="text" name="data[User][email]" />
	<input type="text" name="data[User][phone]" />
	<input type="submit" value="submit" />
</form>
