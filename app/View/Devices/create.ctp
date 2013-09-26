<?php
  $noFormations = count($formations) == 0;
?>
<div id="new-device">
<form class="disabled">
  <div class="select-option">
    <h2>What would you like to do?</h2>
    <div class="input">
      <input type="radio" id="new-formation" name="formation-option" checked />
      <label for="new-formation">Create a new device and a <strong>new formation</strong></label>
    </div>
    <div class="input">
      <input type="radio" id="existing-formation" name="formation-option" <?php if($noFormations) { echo "disabled=\"disabled\""; } ?>/>
      <label for="existing-formation">Add a new device to an <strong>existing formation</strong></label>
    </div>
    <div class="input">
      <select id="select-formation" style="display:none;">
        <option value="">Select a Formation</option>
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
    $('#select-formation').css('display','block'); 
  });
  $('#new-formation').change(function(){
    $('#select-formation').css('display','none');
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
