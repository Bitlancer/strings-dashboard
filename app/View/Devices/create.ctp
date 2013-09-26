<?php
  $noFormations = count($formations) == 0;
?>
<div id="new-device">
<form class="disabled">
  <div class="select-option">
    <h2>What would you like to do?</h2>
    <div class="input">
      <input type="radio" id="new-formation" name="formation-option" checked />
      <label for="new-formation">Create a new device and a new formation</label>
    </div>
    <div class="input">
      <input type="radio" id="existing-formation" name="formation-option" <?php if($noFormations) { echo "disabled=\"disabled\""; } ?>/>
      <label for="existing-formation">Add a new device to</label>
      <select id="select-formation" disabled="disabled">
        <option value="">Select a formation</option>
        <?php foreach($formations as $formation) {
          $name = $formation['Formation']['name'];
          $id = $formation['Formation']['id'];
          ?>
          <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="submit">
    <a class="cta primary">Go</a>
    <a class="cta">Cancel</a>
  </div>
</form>
</div>
<script>
  $('#existing-formation').change(function() {
    $('#select-formation').prop('disabled',false);
  });
  $('#new-formation').change(function(){
    $('#select-formation').prop('disabled',true);
  });
  $('#new-device .cta.primary').click(function(){
    if($('#new-formation').is(':checked')){
      window.location = '/Formations/create';
    }
    else {
      var formationId = $('#select-formation').val();
      window.location = '/Formations/addDevice/' + formationId;
    }
  });
</script>
