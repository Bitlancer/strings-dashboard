<div id="edit-user">
  <ul id="notice">
  </ul>
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI'] . ".json"; ?>">
    <fieldset>
      <legend>Control Panel Privileges</legend>
      <div class="input">
        <input type="radio" id="is_admin" name="data[User][is_admin]" value="1" <?php echo ($user['User']['is_admin'] ? 'checked': ''); ?> />
        <label for="is_admin">Administrator</label>
      </div>
      <div class="input">
        <input type="radio" id="is_user" name="data[User][is_admin]" value="0" <?php echo ($user['User']['is_admin'] ? '' : 'checked'); ?> />
        <label for="is_user" >User</label>
      </div>
    </fieldset>
    <fieldset>
      <legend>Name</legend>
      <input type="text" placeholder="first name" name="data[User][first_name]" value="<?php echo $user['User']['first_name']; ?>" />
      <input type="text" placeholder="last name" name="data[User][last_name]" value="<?php echo $user['User']['last_name']; ?>" />
    </fieldset>
    <fieldset>
      <legend>Details</legend>
      <input type="email" placeholder="email address" name="data[User][email]" value="<?php echo $user['User']['email']; ?>" />
    </fieldset>
    <div class="submit">
      <a class="cta primary submit">Save</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
