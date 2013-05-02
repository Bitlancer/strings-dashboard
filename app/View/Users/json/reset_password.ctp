<div id="reset-password">
  <form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
      <input type="password" placeholder="new password" name="data[User][password]" />
      <input type="password" placeholder="new password (verify)" name="data[User][confirm_password]" />
    <div class="submit">
      <a class="cta primary submit">Reset password</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
