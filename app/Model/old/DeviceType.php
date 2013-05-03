<?php

class DeviceType extends AppModel
{

	public $useTable = 'device_type';

	public $validation = array(
		'type' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Type is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Type cannot be empty'
            ),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This type is already defined'
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
