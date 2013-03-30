<?php

class Config extends AppModel
{
	public $useTable = 'config';

	public $belongsTo = array(
		'Organization'
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
                'rule' => array('checkMultiKeyUniqueness',array('var','organization_id')),
                'message' => 'This var has already been defined for this organization'
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
