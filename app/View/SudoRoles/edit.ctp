<style>
  #edit-sudo-role textarea {
    height:100px;
  }
</style>
<div id="edit-sudo-role">
  <?php echo $this->element('notices'); ?>
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI'] . ".json"; ?>">
    <fieldset>
      <legend>Name</legend>
      <input type="text" placeholder="name" name="data[SudoRole][name]" value="<?php echo $sudoRole['SudoRole']['name']; ?>" />
    </fieldset>
    <fieldset>
      <legend>Run As</legend>
      <input type="text" placeholder="run as" name="data[runas]" value="<?php echo $runas; ?>" disabled />
    </fieldset>
    <fieldset>
      <legend>Commands</legend>
	  <textarea placeholder="ex: /usr/sbin/apachectl, /usr/sbin/tcpdump" name="data[commands]" ><?php echo $commands; ?></textarea>
    </fieldset>
    <div class="submit">
      <a class="cta primary submit">Save</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
