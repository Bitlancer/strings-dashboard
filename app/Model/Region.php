<?php

class Region extends AppModel
{
	public $useTable = 'region';

	public $validate = array(
		'provider_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Provider id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Provider id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Provider id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The provider id you supplied does not exist'
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
            )
		  	'checkMultiKeyUniqueness' => array(
                'rule' => array('checkMultiKeyUniqueness',array('name','provider_id')),
                'message' => 'This region has already been defined for this provider'
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
