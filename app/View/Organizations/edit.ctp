<?php echo $this->Session->flash(); ?>
<form method="post" action="/organizations/edit">
	<input type="text" name="data[Organization][name]" />
	<input type="text" name="data[Organization][short_name]" />
	<input type="submit" value="submit" />
</form>
