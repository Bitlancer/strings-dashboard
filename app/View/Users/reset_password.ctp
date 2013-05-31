  <div id="login"> 
    <h1>Strings</h1>
    <?php echo $this->element('notices'); ?>
    <div data-id="reset-password">
      <p>Enter a new password below.</p>
      <form class="ajax" method="post" action="/Users/resetPassword" >
        <input type="hidden" name="token" value="<?php echo $token; ?>" />
        <input type="password" name="password" placeholder="password" />
        <input type="password" name="confirmPassword" placeholder="confirm password" />
        <span class="buttons">
          <a class="cta submit">reset password</a><a class="cta" href="/login" >Cancel</a>
        </span>
      </form>
    </div> <!-- /password -->
  </div>
