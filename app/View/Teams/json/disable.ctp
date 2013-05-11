<div id="disable-team">
  <form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
    <p>Are you sure you want to disable <?php echo $team['Team']['name']; ?>?</p>
    <div class="submit">
      <a class="cta primary submit">Confirm</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
