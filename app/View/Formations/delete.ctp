<div id="delete-formation">
  <form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] . ".json"; ?>" >
    <p>Are you sure you want to delete <?php echo $formation['Formation']['name']; ?>?</p>
    <div class="submit">
      <a class="cta primary submit">Confirm</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
