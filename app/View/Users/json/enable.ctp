<div id="enable-user">
  <form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
    <p>Are you sure you want to enable <?php echo $user['User']['name']; ?>?</p>
    <div class="submit">
      <a class="cta primary submit">Confirm</a>
      <a class="cta">Cancel</a>
    </div>
  </form>
</div>
