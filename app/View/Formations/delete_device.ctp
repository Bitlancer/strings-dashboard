<div id="delete-device">
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>.json" >
    <ul id="notice"></ul>
    <fieldset class="info">
      <p>
        You are about to delete the device 
        <strong><?php echo $device['Device']['name']; ?></strong>.
        Please proceed with caution since this operation cannot be undone.
      </p>
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
