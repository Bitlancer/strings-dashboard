<?php
$this->extend('/Common/standard');

$this->assign('title','Status');

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Scripts/elements/activity_log');
$this->end();
?>

<!-- Main content -->
<section id="script-status">
<ul id="notice"></ul>
<form>
  <img class="loading" src="/img/loading.gif" />
  <h2 id="status" style="display:inline;"><?php echo $status; ?></h2>
  <hr />
  <pre id="output"><?php echo $output; ?></pre>
</form>
</section>
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
