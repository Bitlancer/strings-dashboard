<div id="edit-team">
  <ul id="notice">
  </ul>
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <?php
      echo $this->element('Associations/fieldset',array(
        'memberData' => array(),
        'fieldsetTitle' => 'Sudo Roles',
        'memberAutocompleteSrc' => '/Users/search',
        'memberFieldName' => 'data[Team][members][]',
        'emptyTableMessage' => 'Add a sudo role above'
      ));
    ?>
    <div class="submit">
      <a class="cta primary submit">Save</a>
      <a class="cta">Cancel</a>
    </div>
  </form>
</div>
