<?php

class Organization extends AppModel {

	public $useTable = 'organization';

	public $validate = array(
        'name' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
				'on' => 'create',
				'required' => true,
				'message' => '%%f is required'
            ),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => '%%f cannot be empty'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This %%f is already registered with another account'
			)
        ),
		'short_name' => array(
			'requiredOnCreate' => array(
				'rule' => 'notEmpty',
				'on' => 'create',
				'required' => true,
				'message' => '%%f is required'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => '%%f cannot be empty'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This %%f is already registered with another account'
			)
		),
		'is_disabled' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => '%%f cannot be empty'
			),
			'isBoolean' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for %%f'
			)
		)
    );
}
