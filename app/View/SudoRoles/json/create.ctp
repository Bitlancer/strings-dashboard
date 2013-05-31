<style>
  #create-sudo-role textarea {
    height:100px;
  }
</style>
<div id="create-sudo-role">
  <ul id="notice">
  </ul>
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <fieldset>
      <legend>Name</legend>
      <input type="text" placeholder="name" name="data[SudoRole][name]" />
    </fieldset>
    <fieldset>
      <legend>Run As</legend>
      <input type="text" placeholder="run as" name="data[runas]" value="root" disabled />
    </fieldset>
    <fieldset>
      <legend>Commands</legend>
      <textarea placeholder="ex: /usr/sbin/apachectl, /usr/sbin/tcpdump" name="data[commands]" ></textarea>
    </fieldset>
    <div class="submit">
      <a class="cta primary submit">Save</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
