  <div id="login"> 
    <h1>Strings</h1>
    <?php echo $this->element('notices'); ?>
    <div data-id="login">
      <form method="post" action="/login">
	    <input type="text" name="data[Organization][short_name]" placeholder="organization" value="<?php echo $organizationShortName; ?>" />
        <input type="text" name="data[User][name]" placeholder="username" value="<?php echo $userName; ?>" />
        <input type="password" name="data[User][password]" placeholder="password" />
        <span class="buttons">
          <a class="cta submit">login</a>
          <input id="remember-me" name="data[User][remember_me]" type="checkbox" <?php if($userRememberMe) { echo "checked=checked"; } ?> />
          <label for="remember-me">remember me</label>
        </span>
      </form>
    </div> <!-- /login -->
    <div data-id="register">
      <p>Strings is in private beta. Enter your email &amp; we'll shoot you a message when we're live.</p>
      <form class="ajax" method="post" action="/Users/register">
        <input type="email" name="email" placeholder="email address" />
        <span class="buttons">
          <a class="cta submit">register</a><a class="cta" data-id="login">cancel</a>
        </span>
      </form>
    </div> <!-- /register -->
    <div data-id="password">
      <p>To reset your password, enter your organization's name and the email
      address associated with your account.</p>
      <form class="ajax" method="post" action="/Users/forgotPassword">
        <input type="text" name="organization" placeholder="organization" />
        <input type="email" name="email" placeholder="email address" />
        <span class="buttons">
          <a class="cta submit">reset password</a><a class="cta" data-id="login">cancel</a>
        </span>
      </form>
    </div> <!-- /password -->
    <span id="tabs">
      <a data-id="register">register</a><a data-id="password">forgot password</a>
    </span>
  </div>
