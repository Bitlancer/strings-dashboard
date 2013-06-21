<div id="enable-team">
  <form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] . ".json"; ?>" >
    <p>Are you sure you want to enable <?php echo $team['Team']['name']; ?>?</p>
    <div class="submit">
      <a class="cta primary submit">Confirm</a>
      <a class="cta">Cancel</a>
    </div>
  </form>
</div>
