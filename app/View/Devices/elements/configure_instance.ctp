<?php
/**
 * Provides the accordion configuration element
 * seen anywhere puppet variables are configured.
 */

    if(!isset($variableDefs))
        $variableDefs = array();

    if(!isset($variableErrors))
        $variableErrors = array();

    if(isset($inputPrefix))
        $inputPrefix  .= '.';
    else
        $inputPrefix = '';
?>
<div class="accordion" data-active="false">
<?php foreach($variableDefs as $module){
  $moduleId = $module['id'];
  $moduleName = $module['shortName'];
  $variables = $module['variables'];
  $inErrorState = isset($variableErrors[$moduleId]);
  ?>
  <h3 class="<?php echo $inErrorState ? 'error' : ''; ?>">
    <span class="title"><?php echo $moduleName; ?></span>
  </h3>
  <div class="module">
    <?php foreach($variables as $var){
      $varId = $var['id'];
      $label = $var['name'];
      $description = htmlentities($var['description']);
      $inputName = $inputPrefix . "variables.$moduleId.$varId";
      $defaultValue = $var['default_value'];
      $required = $var['is_required'] ? true : false;
      $editable = $var['is_editable'] ? true : false;
      $varInErrorState = isset($variableErrors[$moduleId][$varId]);
      $varErrorMsg = $varInErrorState ? $variableErrors[$moduleId][$varId] : "";
      ?>
      <div class="variable input <?php echo $varInErrorState ? "error" : ""; ?>">
        <?php echo $this->Form->input($inputName,array(
          'label' => array(
            'text' => $label,
            'class' => 'tooltip',
            'title' => $description
          ),
          'div' => false,
          'default' => $defaultValue,
          'required' => $required,
          'readonly' => !$editable
        )); ?>
        <span class="error-msg"><?php echo $varErrorMsg; ?></span>
        
      </div> <!-- /.variable -->
    <?php } ?>
  </div> <!-- /.module -->
<?php } ?>
</div> <!-- /.accordion -->
