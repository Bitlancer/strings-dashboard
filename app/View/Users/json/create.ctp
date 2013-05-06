<div id="create-user">
  <ul id="notice">
  </ul>
  <form class="ajax" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
	<fieldset>
      <legend>Account</legend>
      <input type="text" placeholder="username" name="data[User][name]" />
      <input type="password" placeholder="password" name="data[User][password]" />
      <input type="password" placeholder="confirm password" name="data[User][confirm_password]" />
    </fieldset>
    <fieldset>
      <legend>Name</legend>
      <input type="text" placeholder="first name" name="data[User][first_name]" />
      <input type="text" placeholder="last name" name="data[User][last_name]" />
    </fieldset>
    <fieldset>
      <legend>Details</legend>
      <input type="email" placeholder="email address" name="data[User][email]" />
    </fieldset>
    <div class="submit">
      <a class="cta primary submit">Confirm</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
