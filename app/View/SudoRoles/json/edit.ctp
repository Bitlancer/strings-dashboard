<div id="edit-sudo-role">
  <?php echo $this->element('notices'); ?>
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
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
      <input type="text" placeholder="commands (ex:/usr/sbin/apachectl,/usr/sbin/tcpdump)" name="data[commands]" value="<?php echo $commands; ?>" />
    </fieldset>
    <div class="submit">
      <a class="cta primary submit">Save</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
