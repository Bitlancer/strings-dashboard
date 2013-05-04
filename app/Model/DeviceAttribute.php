<?php

class DeviceAttribute extends AppModel {

	public $useTable = 'device_attribute';

	public $belongsTo = array(
		'Organization',
		'Device'
	);

	public $hasMany = array();

	public $hasAndBelongsToMany = array();

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
                'message' => 'The device you supplied does not exist'
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
				'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
				'message' => 'This var has already been defined for this device'
			)
        )
    );
}
