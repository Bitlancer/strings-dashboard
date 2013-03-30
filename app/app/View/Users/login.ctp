  <form id="login" method="post" action="/login">
    <h1>Strings</h1>
	<div style="font-weight:bold;">
	  <?php
	    echo $this->Session->flash();
	    echo $this->Session->flash('auth');
	  ?>
	</div>
    <div data-id="login">
      <input type="text" name="data[Organization][short_name]" placeholder="organization" />
      <input type="text" name="data[User][name]" placeholder="username" />
      <input type="password" name="data[User][password]" placeholder="password" />
      <span class="buttons">
        <a class="cta submit">login</a>
        <input id="remember-me" type="checkbox" checked=checked />
        <label for="remember-me">remember me</label>
      </span>
    </div>
    <div data-id="register">
      <p>Strings is in private beta. Enter your email &amp; we'll shoot you a message when we're live.</p>
      <input type="email" placeholder="email address" />
      <span class="buttons">
        <a class="cta">register</a><a class="cta" data-id="login">cancel</a>
      </span>
    </div>
    <div data-id="password">
      <input type="email" placeholder="email address" />
      <span class="buttons">
        <a class="cta">reset password</a><a class="cta" data-id="login">cancel</a>
      </span>
    </div>
    <span id="tabs">
      <a data-id="register">register</a><a data-id="password">forgot password</a>
    </span>
  </form>
