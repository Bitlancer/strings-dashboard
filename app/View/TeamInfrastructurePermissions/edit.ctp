<?php
/*
Render the content responsible for editing user infrastructure permissions
*/
?>
<div id="edit-permissions">
<form class="disabled" data-model="<?php echo $model; ?>" data-id="<?php echo $entityId; ?>">
  <ul id="notice"></ul>
  <div id="select-team">
    <label>Set privileges for</label>
    <select id="team" name="data[Team][id]">
      <option value="">Select a team</option>
      <?php foreach($teams as $team){
        $teamId = $team['Team']['id'];
        $teamName = $team['Team']['name'];
        ?>
        <option value="<?php echo $teamId; ?>" ><?php echo $teamName; ?></option>
      <?php } ?>
    </select>
  </div>
  <fieldset id="privileges">
  <legend>Privileges</legend>
    <div class="input">
      <input id="grantLogin" type="checkbox" name="grantLogin" disabled />
      <label for="grantLogin">Grant login privileges</label>
    </div>
    <div>
      <input id="grantSudo" type="checkbox" name="grantSudo" disabled />
      <label for="grantSudo">Grant sudo privileges</label>
    </div>
  </fieldset>
  <fieldset id="sudo-roles">
    <legend>Sudo Privileges</legend>
    <div class="association disabled">
      <div id="add">
        <input type="text" class="autocomplete" placeholder="name" data-src="/SudoRoles/searchByName" />
        <a class="cta primary small add disabled">Add</a>
      </div>
      <table data-paginate="false" data-processing="true">
        <thead><tr><th>Sudo Role</th></tr></thead>
        <tbody><tr><td class="blank">No data available</td></tr></tbody>
      </table>
    </div>
  </fieldset>
</form>
</div>
<script>

var permissions = function(container){

  var container = $(container);

  var syncStateWithUi = function(e){

    var src = (e && e.target) || (window.event && window.event.srcElement);
    src = $(src);

    //Retrieve DOM elements
    var form = container.find('form');
    var table = container.find('table');
    var sudoRoles = container.find('#sudo-roles');
    var association = container.find('.association');

    var select = container.find('select');
    var grantLogin = container.find('#grantLogin');
    var grantSudo = container.find('#grantSudo');

    var resync = src.is('select');

    strings.notifications.empty();

    //Determine various sources
    var teamId = select.val();
    var model = form.attr('data-model');
    var entityId = form.attr('data-id');
    var statusSrc = '/TeamInfrastructurePermissions/editTeamPermissions/' + model + '/' + entityId + '/' + teamId + '.json';
    var tableSrc = '/TeamInfrastructurePermissions/teamSudoRoles/' + model + '/' + entityId + '/' + teamId + '.json';
    var addSrc = '/TeamInfrastructurePermissions/addSudoRoleToTeam/' + model + '/' + entityId + '/' + teamId;
    var removeSrc = '/TeamInfrastructurePermissions/removeSudoRoleFromTeam/' + model + '/' + entityId + '/' + teamId;
  
    //Update element srcs if user just changed teams
    if(src.is('select')){
      association.attr('data-src-add',addSrc);
      association.attr('data-src-remove',removeSrc);
      table.attr('data-src',tableSrc);
    }

    //First change logic
    var blankOption = select.find("option[value='']");
    if(blankOption.length){
      select.find("option[value='']").remove();
      grantLogin.prop('disabled',false);
    }

    $.ajax({
      type: resync ? 'get' : 'post',    //Get retrieves values; POST will update them
      url: statusSrc,
      data: {
        "grantLogin": grantLogin.is(":checked"),
        "grantSudo": grantSudo.is(":checked")
      },
      dataType: 'json',
      success: function(response){
        if(response.isError){
          strings.notifications.alert(response.message);
        }
        grantLogin.prop('checked',response.grantLogin);
        grantSudo.prop('checked',response.grantSudo);

        //Set grantSudo state based on grantLogin
        if(grantLogin.is(':checked')) {
          grantSudo.prop('disabled', false);
        }
        else {
          grantSudo.prop('disabled',true);
        }

        //Set table state
        table.dataTable().fnDestroy();
        strings.ui.tables.attach(table);
        if(grantSudo.is(":checked")){
          strings.associations.enable(association);
        }
        else {
          strings.associations.disable(association);
        }
      }
    })
    .error(function(jqXHR,textStatus){
      strings.notifications.unexpectedError();
      console.log(textStatus);
    });

  };

  $(container).find('select').on('change',syncStateWithUi);

  $(container).find('#grantLogin').change(syncStateWithUi);

  $(container).find('#grantSudo').change(syncStateWithUi);

};

permissions('#edit-permissions');

</script>
