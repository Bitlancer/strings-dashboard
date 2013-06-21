<div id="delete-application">
  <form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] . ".json"; ?>" >
    <p>Are you sure you want to delete <?php echo $application['Application']['name']; ?>?</p>
    <div class="submit">
      <a class="cta primary submit">Confirm</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
