<div>
  <a id="logo" href="/">Strings</a>
  <ul class="action-menu" id="menu" data-width="140">
    <li>
      <?php echo $this->Gravatar->image($this->Session->read('Auth.User.email'),array('s' => 20)); ?>
      <?php echo $this->Session->read('Auth.User.full_name'); ?>
    </li>
    <span>
      <a class="modal" data-src="/users/mySettings" data-title="My Settings">Settings</a>
      <a href="/logout">Sign Out</a>
    </span>
  </ul>
</div>
