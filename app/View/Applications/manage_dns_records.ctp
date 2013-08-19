<div id="manage-dns-records">
  <ul id="notice"></ul>
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI'] . ".json"; ?>" data-app-id="<?php echo $application['Application']['id']; ?>">
    <div id="select-device">
      <label>Manage records for</label>
      <select id="device" name="data[Device][id]">
        <option value="">Select a device</option>
        <?php foreach($devices as $device){
          $deviceId = $device['id'];
          $deviceName = $device['name'];
        ?>
        <option value="<?php echo $deviceId; ?>" ><?php echo $deviceName; ?></option>
        <?php } ?>
      </select>
    </div>
    <fieldset>
      <legend>Records</legend>
      <div class="association disabled">
        <div id="add">
          <input type="text" placeholder="name" />
          <a class="cta primary small add disabled">Add</a>
        </div>
        <table data-paginate="false" data-processing="true">
          <thead><tr><th>Record</th></tr></thead>
          <tbody><tr><td class="blank">No data available</td></tr></tbody>
        </table>
      </div>
    </fieldset>
  </form>
</div>

<script>

var manageDns = function(container){
    
  var syncState = function(container){
    
    //Get nodes
    var form = container.find('form');
    var select = container.find('select');
    var association = container.find('.association');
    var table = container.find('table');
    
    strings.notifications.empty();
    
    //First select change logic
    var blankOption = select.find("option[value='']");
    if(blankOption.length){
      select.find("option[value='']").remove();
      strings.associations.enable(association);
    }
    
    //Update association srcs
    var applicationId = form.attr('data-app-id');
    var deviceId = select.val();
    association.attr('data-src-add','/Applications/addDnsRecord/' + applicationId + '/' + deviceId);
    association.attr('data-src-remove','/Applications/removeDnsRecord/' + applicationId);
    table.attr('data-src','/Applications/manageDeviceDnsRecords/' + applicationId + '/' + deviceId);
    
    //Update datatable  
    table.dataTable().fnDestroy();
    strings.ui.tables.attach(table);
  };
  
  var container = $(container);
  var select = container.find('select');
  
  select.change(function(){
    syncState(container)
  });
}

manageDns('#manage-dns-records');

</script>
