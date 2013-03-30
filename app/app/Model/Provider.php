<?php

class Provider extends AppModel
{
	public $useTable = 'provider';

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
