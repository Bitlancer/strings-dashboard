<?php

App::uses('Model', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class AppModel extends Model {

    /*
     * Most models with a name attribute will use the following validation variables
     */
    const VALID_MODEL_NAME_REGEX = '/^[A-Za-z0-9 -]{3,}$/';
    const VALID_MODEL_NAME_MSG = '%%f is limited to uppercase and lowercase letters, numbers, hypens and spaces and must be at least 3 characters long';

    /*
     * Stores row ids for any newly created models
     */
    private $insertIds = array();

    /*
     * Behaviors
     */
	public $actsAs = array(
		'Containable',
		'Linkable',
		'ExtendAssociations2',
        'AuditLog.Auditable' => array(
            'ignore' => array('created','updated')
        )
	);

/*
 * Callbacks
 */

    /**
     * Before save callback
     */
    public function beforeSave($options=array()){

        $this->overrideBadTimestamps();
        return true;
    }

    /**
     * Override any bad datetime columns
     *
     * Why do I exist? When create() is called on a model, CakePHP will
     * grab the default value for each column from the schema. Anywhere
     * a datetime column has been defined with a default value of 
     * CURRENT_TIMESTAMP, Cake will pull that in however it will treat
     * it like a string and escape it with quotes on insertion. This
     * function exists to replace the string CURRENT_TIMESTAMP with
     * an actual time string.
     *
     */
    protected function overrideBadTimestamps(){

        $columns = $this->schema();
        foreach($columns as $columnName => $column){
            if($column['type'] == 'datetime' &&
                $column['default'] == 'CURRENT_TIMESTAMP' &&
                isset($this->data[$this->alias][$columnName]) &&
                $this->data[$this->alias][$columnName] == 'CURRENT_TIMESTAMP'
            ){
                $this->data[$this->alias][$columnName] = date('Y-m-d H:i:s');   
            }
        }
    }

    /**
     * After save callback
     */
    public function afterSave($created, $options=array()){

        if($created) {
            $this->addInsertId($this->getInsertID());
        }

        return true;
    }

/*
 * Validation
 */

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

/*
 * Misc
 */

    /**
     * Override CakePHP's native exists method.
     *
     * Most of this App's models behave as "OrganizationOwned" models.
     * The "OrganizationOwned" behavior takes care of appending organization_id
     * to each query to securely handle data separation between organizations.
     * CakePHP's native exists method bypasses this behavior by using a special
     * query which does not trigger the behavior callbacks. By overriding this
     * method we can force the callbacks to be executed and enfore the logic
     * defined by "OrganizationOwned".
     */
    public function exists($id=false){

        if($id === false)
            $id = $this->getID();
    
        if($id === false)
            return false;

        $model = $this->find('first',array(
            'contain' => array(),
            'conditions'=> array(
                'id' => $id
            )
        ));

        if(empty($model))
            return false;

        return true;
    }

    /**
     * Return the last SQL statement executed
     */
    public function getSQLLog(){

        return $this->getDataSource()->getLog(false,false);
    }

    /**
     * Manually escape input values
     */
    public function escapeValue($value,$type = 'string'){

        return $this->getDataSource()->value($value,$type);
    }

    /**
     * Get the current user
     * Used by the AuditLog plugin
     */
    public function currentuser(){

        return CakeSession::read('Auth.User');
    }

    /**
     * Return a list of ids for any newly created models
     */
    public function getInsertIds(){
        return $this->insertIds;
    }

    /**
     * Append an id to the list of newly created models
     */
    public function addInsertId($id){
        $this->insertIds[] = $id;
    }

}
