<?php

$this->extend('/Common/standard');

$this->assign('title', 'Queue Job Status');

//Set sidebar content
$this->start('sidebar');
echo $this->element('../QueueJobs/elements/activity_log');
$this->end();
?>

<?php
  $jobOutput = $job['QueueJobLog'][0]['msg'];
?>
<div id="queue-job-status">
<form>
  <fieldset>
    <legend>Job Details</legend>
    <table id="job-details">
    <tbody>
    </tbody>
    </table>
  </fieldset>
  <fieldset>
    <legend>Output</legend>
    <pre id="output" class="wrap"><?php echo $jobOutput; ?></pre>
  </fieldset>
</form>
</div>
