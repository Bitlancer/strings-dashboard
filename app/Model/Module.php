<?php

class Module extends AppModel
{

	public $useTable = 'module';

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
                'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
                'message' => 'A module with this name is already defined'
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
