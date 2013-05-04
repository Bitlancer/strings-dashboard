<?php

class Device extends AppModel {
	
	public $useTable = 'device';

	public $belongsTo = array(
		'Organization',
		'Implementation',
		'Formation',
		'Role'
	);

	public $hasMany = array(
		'DeviceAttribute'
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
		'implementation_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Implementation id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Implementation id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Implementation id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The implementation you supplied does not exist'
            )
        ),
		'formation_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Formation id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Formation id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Formation id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The formation you supplied does not exist'
            )
        ),
		'role_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Role id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Role id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Role id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The role you supplied does not exist'
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
			'validHostname' => array(
				'rule' => array('custom', '/^(?![0-9]+$)(?!-)[a-zA-Z0-9-]{,63}(?<!-)$/'),
				'message' => 'Please enter a valid hostname'
			),
			'checkMultiKeyUniqueness' => array(
                'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
                'message' => 'This device name is already taken'
            )
		)
	);
}
