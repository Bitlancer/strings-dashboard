<?php
  $userId = $this->Session->read('Auth.User.id');
  $userEmail = $this->Session->read('Auth.User.email');
  $userFullName = $this->Session->read('Auth.User.full_name');
?>
<div>
  <a id="logo" href="/">Strings</a>
  <ul class="action-menu" id="menu" data-width="140">
    <li>
      <?php echo $this->Gravatar->image($userEmail,array('s' => 20)); ?>
      <?php echo $userFullName; ?>
    </li>
    <span>
      <a class="modal" data-src="/users/changePassword/<?php echo $userId; ?>" data-title="Change Password">Change Password</a>
      <a href="/users/sshKeys/<?php echo $userId; ?>">SSH Keys</a>
      <a href="/logout">Sign Out</a>
    </span>
  </ul>
</div>
