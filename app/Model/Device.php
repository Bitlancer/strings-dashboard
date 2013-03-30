<?php

class Device extends AppModel {
	
	public $useTable = 'device';

	public $belongsTo = array(
		'Organization',
		'DeviceType'
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
                'message' => 'The organization id you supplied does not exist'
            )
        ),
		'type_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Type id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Type id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Type id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The type id you supplied does not exist'
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
		),
		'is_active' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
				'message' => 'is_active cannot be empty'
            ),
            'isBoolean' => array(
                'rule' => 'boolean',
                'message' => 'Invalid value for is_active'
            )
        )
	);
}
