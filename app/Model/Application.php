<?php

class Application extends AppModel {

	public $useTable = 'application';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization'
	);

	public $hasMany = array(
		'TeamApplication' => array(
            'dependent' => true
        ),
        'ApplicationFormation' => array(
            'dependent' => true
        ),
        'Script' => array(
            'foreignKey' => 'foreign_key_id',
            'conditions' => array(
                'Script.model' => 'application',
            ),
            'dependent' => true
        ),
	);

	public $hasAndBelongsToMany = array();

	public $validate = array(
        'name' => array(
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
            'validName' => array(
                'rule' => AppModel::VALID_MODEL_NAME_REGEX,
                'message' => AppModel::VALID_MODEL_NAME_MSG
            ),
			'checkMultiKeyUniqueness' => array(
				'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
				'message' => 'This %%f is already taken'
			)
        )
    );

    public function beforeSave($options = array()){

        $data = $this->data[$this->alias];

        if(isset($data['name'])){
            $this->data[$this->alias]['name'] = strtolower($data['name']);
        }

        return true;
    }

    public function afterFind($results,$primary = false){

        if($primary !== false){
            foreach($results as $key => $result){

                //ucwords() the name
                if(isset($result[$this->alias]['name'])) {
                    $name = ucwords($result[$this->alias]['name']);
                    $results[$key][$this->alias]['name'] = $name;
                }
            }
        }
        else {

            //ucwords() the name
            if(isset($results['name'])){
                $results['name'] = ucwords($results['name']);
            }
        }

        return $results;
    }

}
