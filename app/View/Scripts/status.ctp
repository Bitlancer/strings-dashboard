<h1>Status</h1>
<div id="script-status">
<ul id="notice"></ul>
<form>
  <img class="loading" src="/img/loading.gif" />
  <h2 id="status" style="display:inline;"><?php echo $status; ?></h2>
  <hr />
  <pre id="output"><?php echo $output; ?></pre>
</form>
</div>
<script>
  var refreshStatus = function(){
    $.ajax({
      type: 'post',
      url: '<?php echo $this->here . ".json"; ?>',
      success: function(data){
        $('#status').text(data.status);
        $('#output').text(data.output);
        if(!data.hasCompleted){
          setTimeout(refreshStatus,4000);
        }
        else {
          $('#script-status .loading').css('display','none');
        }
      },
    })
    .error(function(jqXHR,textStatus){
      strings.notifications.alert('We encountered an unexpected error.');
      console.log(textStatus);
    });
  }; 
  setTimeout(refreshStatus,2000);
</script>
