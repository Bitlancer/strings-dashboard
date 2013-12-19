<?php
    $memberData = array();
    foreach($members as $member){

        $member['id'] = $member['User']['id'];
        $member['name'] = $member['User']['name'];

        $memberData[] = $member;
    }
?>
<div id="edit-team">
  <ul id="notice"></ul>
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI'] . ".json"; ?>">
    <fieldset>
      <legend>Name</legend>
      <input type="text" placeholder="name" name="data[Team][name]" value="<?php echo $team['Team']['name']; ?>" />
    </fieldset>
    <?php
      echo $this->element('Associations/fieldset',array(
        'memberData' => $memberData,
        'memberAutocompleteSrc' => '/Users/search',
        'memberFieldName' => 'data[Team][members][]'
      ));
    ?>
    <div class="submit">
      <a class="cta primary submit">Save</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
