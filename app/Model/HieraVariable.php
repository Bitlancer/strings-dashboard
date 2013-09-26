<?php

class HieraVariable extends AppModel {

	public $useTable = 'hiera';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization',
	);

	public $validate = array(
        'hiera_key' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
        ), 
        'var' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
        ),
        'val' => array(),
    );

    public function parseAndValidateDeviceVariables($modulesVariables,$input,$hieraKey){

        $variables = array();
        $errors = array();

        foreach($modulesVariables as $moduleId => $module){

            $moduleErrors = array();
            $moduleShortName = $module['shortName'];

            foreach($module['variables'] as $varId => $var){

                $hieraVar = $var['var'];
                $varName = $var['name'];
                $varFullName = ucfirst($moduleShortName) . " " . ucfirst($varName);
                $varValidationPattern = $var['validation_pattern'];

                //Check if variable is set
                if(isset($input[$moduleId]) && isset($input[$moduleId][$varId]) && 
                   (is_numeric($input[$moduleId][$varId]) || !empty($input[$moduleId][$varId]))){

                    $deviceVarVal = $input[$moduleId][$varId];

                    //If variable is not editable, overwrite input value with
                    //the default value
                    if(!$var['is_editable']){
                        $deviceVarVal = $var['default_value'];
                    }

                    //Validate variable value if needed
                    if(!empty($varValidationPattern) && !preg_match($varValidationPattern,$deviceVarVal))
                        $moduleErrors[$varId] = "Invalid value.";
                    else {
                        $variables[] = array(
                            'hiera_key' => $hieraKey,
                            'var' => $hieraVar,
                            'val' => $deviceVarVal
                        );
                    }
                }
                else { //Verify variable is not required
                    if($var['is_required'])
                        $moduleErrors[$varId] = "Variable is required.";
                }
            }

            if(!empty($moduleErrors))
                $errors[$moduleId] = $moduleErrors;
        }

        return array($variables,$errors);
    }
}
