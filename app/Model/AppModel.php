<?php

App::uses('Model', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class AppModel extends Model {

    /**
     * Most models with a name attribute will use the following validation variables
     */
    const VALID_MODEL_NAME_REGEX = '/^[A-Za-z0-9 -]{3,}$/';
    const VALID_MODEL_NAME_MSG = '%%f is limited to uppercase and lowercase letters, numbers, hypens and spaces and must be at least 3 characters long';

	public $actsAs = array(
		'Containable',
		'Linkable',
		'ExtendAssociations2',
        'AuditLog.Auditable' => array(
            'ignore' => array('created','updated')
        )
	);

	/**
	 * Validate relationship (foreign_key) between this model and belongsTo model
 	 */
	public function isValidForeignKey($data) {
    	foreach ($data as $key => $value) {
        	foreach ($this->belongsTo as $alias => $assoc) {
            	if ($assoc['foreignKey'] == $key) {
                	$this->{$alias}->id = $value;
                	return $this->{$alias}->exists();
            	}
        	}
    	}
    	return false;
	}

	/**
	 * Validate multikey unique constraint
	 */
	public function checkMultiKeyUniqueness($data, $fields){
	
		// check if the param contains multiple columns or a single one
		if (!is_array($fields))
			$fields = array($fields);
		 
		// go through all columns and get their values from the parameters
		foreach($fields as $key)
			$unique[$key] = $this->data[$this->name][$key];
		 
		// primary key value must be different from the posted value
		if (isset($this->data[$this->name][$this->primaryKey]))
			$unique[$this->primaryKey] = "<>" . $this->data[$this->name][$this->primaryKey];
		 
		// use the model's isUnique function to check the unique rule
		return $this->isUnique($unique, false);
	}

	/**
     * Get validationErrors as a string
     *
     * @return string The validation errors data structure converted to a string
     */
    public function validationErrorsAsString($saveMany=false){

		if($saveMany){
			return print_r($this->validationErrors,true);
		}
		else {
			$message = "";
        	foreach($this->validationErrors as $field => $fieldErrorMessages){
				array_walk_recursive($fieldErrorMessages,function(&$element,$index) use($field) {
                    $field = strtolower(Inflector::humanize($field));
                    $element = str_replace('%%f',$field,$element);
					$element = str_replace('%f',$field,$element);
					$element = ucfirst($element);
				});
            	$message .= implode(". ",$fieldErrorMessages) . ". ";
			}
        	return $message;
		}
    }

    /**
     * Return the last SQL statement executed
     */
    public function getSQLLog(){

        return $this->getDataSource()->getLog(false,false);
    }

    /**
     * Get the current user
     * Used by the AuditLog plugin
     */
    public function currentuser(){

        return CakeSession::read('Auth.User');
    }
}
