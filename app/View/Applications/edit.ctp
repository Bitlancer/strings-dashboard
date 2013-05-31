<?php
$this->extend('/Common/standard');

$this->assign('title', 'Application Permissions');

//Set sidebar content
$this->start('sidebar');
echo $this->element('activity_log',array(
    'activityLogUri' => ''
));
$this->end();
?>

<div id="edit-application">
  <h2>Application</h2>
  <ul id="notice">
  </ul>
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <fieldset>
      <legend>Name</legend>
      <input type="text" placeholder="name" name="data[Application][name]" value="<?php echo $application['Application']['name']; ?>" />
    </fieldset>
    <div class="submit">
      <a class="cta primary submit">Save</a>
      <a class="cta">Cancel</a>
    </div>
  </form>
</div>
<div>
  <h2>Members</h2>
 <?php
      $memberData = array();
      echo $this->element('Associations/fieldset',array(
        'memberData' => $memberData,
        'memberAutocompleteSrc' => '/Users/search',
        'memberFieldName' => 'data[Team][members][]'
      ));
    ?>
</div>
