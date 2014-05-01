<?php

class Formation extends AppModel {

	public $useTable = 'formation';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization',
        'Implementation',
        'Blueprint',
        'Environment',
	);

	public $hasMany = array(
		'Device',
		'TeamFormation' => array(
            'dependent' => true
        ),
        'ApplicationFormation' => array(
            'dependent' => true
        ),
        'Script' => array(
            'foreignKey' => 'foreign_key_id',
            'conditions' => array(
                'Script.model' => 'Formation',
            ),
            'dependent' => true
        ),
	);

	public $hasAndBelongsToMany = array();

	public $validate = array(
        'implementation_id' => array(
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
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%%f must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => '%%f does not exist'
            )
        ),
        'blueprint_id' => array(
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
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%%f must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => '%%f does not exist'
            )
        ),
        'environment_id' => array(
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
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%%f must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => '%%f does not exist'
            )
        ),
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
        ),
        'status' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
            'validStatus' => array(
                'rule' => array('inList',array('building','resizing','active','deleting','error')),
                'message' => '%%f is an invalid status'
            )
        )
    );

    public function beforeSave($options = array()){

        if(!parent::beforeSave($options))
            return false;

        $data = $this->data[$this->alias];

        if(isset($data['name'])){
            $this->data[$this->alias]['name'] = strtolower($data['name']);
        }

        return true;
    }

    public function afterFind($results,$primary = false){

        //ucwords(name)
        if(isset($results['name'])){
            $results['name'] = ucwords($results['name']);
        }
        else {
            foreach($results as $key => $result){
                if(isset($result[$this->alias]['name'])) {
                    $name = ucwords($result[$this->alias]['name']);
                    $results[$key][$this->alias]['name'] = $name;
                }
            }
        }
        return $results;
    }

}
