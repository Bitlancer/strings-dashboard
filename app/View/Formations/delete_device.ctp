<div id="delete-device">
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>.json" >
    <ul id="notice"></ul>
    <fieldset class="info">
      <p>You are about to <strong>delete <?php echo $device['Device']['name']; ?></strong>. <strong>This operation cannot be undone.</strong></p>
    </fieldset>
    <fieldset>
      <legend>Enter the device name to confirm</legend>
      <input name="confirm" type="text" placeholder="device name" />
    </fieldset>
    <div class="submit">
      <a class="cta cancel submit">Delete device</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
