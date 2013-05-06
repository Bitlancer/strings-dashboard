<?php

class Provider extends AppModel {

	public $useTable = 'provider';

	public $hasAndBelongsToMany = array(
        'Service' => array(
            'className' => 'Service',
            'joinTable' => 'service_provider',
            'foreignKey' => 'provider_id',
            'associationForeignKey' => 'service_id'
        )
    );

	public $validate = array(
        'organization_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Organization id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Organization id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Organization id must be an integer'
            ),
			'validForeignKey' => array(
				'rule' => array('isValidForeignKey'),
				'message' => 'The organization you supplied does not exist'
			)
        ),
		'service_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Service id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Service id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Service id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The service you supplied does not exist'
            )
        ),	
        'name' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Name is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Name cannot be empty'
            ),
            'validName' => array(
                'rule' => array('custom','/[A-Za-z0-9-_\. @]{3,}/'),
                'message' => 'Name is limited to letters, numbers and punctuation and must be at least 3 characters long'
            ),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This name is already taken'
			)
        )
    );

    public function beforeSave($options = array()) {

        return true;
    }
}
