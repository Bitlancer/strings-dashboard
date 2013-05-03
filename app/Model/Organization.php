<?php

class Organization extends AppModel {

	public $useTable = 'organization';

	public $validate = array(
        'name' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
				'on' => 'create',
				'required' => true,
				'message' => 'Organization name is required'
            ),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Organization name cannot be empty'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This name is already registered with another account'
			)
        ),
		'short_name' => array(
			'requiredOnCreate' => array(
				'rule' => 'notEmpty',
				'on' => 'create',
				'required' => true,
				'message' => 'Organization short name is required'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Organization short name cannot be empty'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This name is already registered with another account'
			)
		),
		'is_disabled' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'is_disabled cannot be empty'
			),
			'isBoolean' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for is_disabled'
			)
		)
    );
}
