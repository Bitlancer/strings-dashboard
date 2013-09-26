<?php
  $scriptName = isset($script) ? $script['Script']['name'] : "";
  $scriptParameters = isset($script) ? $script['Script']['parameters'] : "";
  $scriptRepoUrl = isset($script) ? $script['Script']['url'] : "";
  $scriptPath = isset($script) ? $script['Script']['path'] : "";
?>
<div id="create-edit-script">
  <ul id="notice"></ul>
  <form class="ajax"
    method="POST"
    action="<?php echo $_SERVER['REQUEST_URI'];  ?>.json">
    <fieldset>
      <legend>Name</legend>
      <input type="text"
        placeholder="ex: Drupal 7 stable branch"
        name="data[Script][name]"
        value="<?php echo $scriptName; ?>"
        required />
    </fieldset>
    <fieldset>
      <legend>Source</legend>
      <select name="data[Script][type]" required>
        <option value="git">Git</option>
      </select>
    </fieldset>
    <fieldset>
      <legend>Git Parameters</legend>
      <p class="help">
        The <strong>repository url</strong>
        (ex: git@github.com:bitlancer/deploy-example.git) 
        is the url you would use to clone the respository via ssh.
        The <strong>path</strong> (ex: directory/deploy.sh) is the relative 
        path to the script within the git repository. If your script is
        is living in the root of the respository enter the filename.
      </p>
      <input type="text"
        placeholder="repository url"
        name="data[Script][url]"
        value="<?php echo $scriptRepoUrl; ?>"
        required/>
      <input type="text"
        placeholder="script path"
        name="data[Script][path]"
        value="<?php echo $scriptPath; ?>"
        required/>
    </fieldset>
    <fieldset>
      <legend>Script Parameters</legend>
      <p class="help">
        An optional string of parameters that will be passed to the script
        at execution.
      </p>
      <textarea 
        placeholder="ex: -vvv -d test"
        name="data[Script][parameters]"><?php echo $scriptParameters; ?></textarea>
      </textarea>
    </fieldset>
    <div class="submit">
      <a class="cta primary submit">Save</a>
      <a class="cta">Cancel</a>
    </div>
  </form>          
</div>
