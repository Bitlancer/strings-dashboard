<?php

class DeviceMeta extends AppModel {

	public $useTable = 'device_meta';

	public $belongsTo = array(
		'Device'
	);

	public $validate = array(
        'device_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Device id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Device id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Device id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The device id you supplied does not exist'
            )
        ),
		'var' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Var is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Var cannot be empty'
            ),
			'checkMultiKeyUniqueness' => array(
                'rule' => array('checkMultiKeyUniqueness',array('var','device_id')),
                'message' => 'This var has already been defined for this device'
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
