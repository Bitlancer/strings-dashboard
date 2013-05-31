<div id="create-team">
  <ul id="notice">
  </ul>
  <form class="ajax" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <fieldset>
      <legend>Name</legend>
      <input type="text" placeholder="name" name="data[Team][name]" />
    </fieldset>
    <?php
      echo $this->element('Associations/fieldset',array(
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
