<div id="resize-device">
<form class="ajax" method="post" action="<?php echo $this->here . ".json"; ?>" >
  <ul id="notice"></ul>
  <p>Notice: Resizing a device can take upward of an hour to complete.</p>
  <select name="flavor" style="width:auto;">
  <?php foreach($flavors as $flavor) { ?>
    <option value="<?php echo $flavor['id']; ?>"><?php echo $flavor['description']; ?></option>
  <?php } ?>
  </select>
  <div class="submit">
    <a class="cta primary submit">Save</a>
    <a class="cta">Cancel</a>
  </div>
</form>
</div>
