<?php

class Environment extends AppModel {

	public $useTable = 'environment';

    public $actsAs = array(
        'OrganizationOwned'
    );

    public $hasMany = array(
        'Device',
    );

    public $hasOne = array(
        'Dictionary'
    );

	public $validate = array(
        'dictionary_id' => array(
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
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%%f must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => '%%f does not exist'
            )
        ),
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
				'message' => 'This %%f is already in use'
			)
        ),
    );
}
