<?php

class Service extends AppModel {

	public $useTable = 'service';

	public $hasAndBelongsToMany = array(
        'Provider' => array(
            'className' => 'Provider',
            'joinTable' => 'service_provider',
            'foreignKey' => 'service_id',
            'associationForeignKey' => 'provider_id'
        )
    );

	public $validate = array(
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
				'rule' => 'isUnique'
				'message' => 'This name is already taken'
			)
        )
    );
}
