<div id="delete-formation">
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>.json" >
    <ul id="notice"></ul>
    <fieldset class="info">
      <p>You are about to <strong>delete the Formation <?php echo $formation['Formation']['name']; ?></strong> and all of its instances. <strong>This operation cannot be undone.</strong> We highly recommend you run a full backup of each instance before deleting this formation.</p>
    </fieldset>
    <fieldset>
      <legend>Enter the formation name to confirm</legend>
      <input name="confirm" type="text" placeholder="formation name" />
    </fieldset>
    <div class="submit">
      <a class="cta cancel submit">Delete formation</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
