<?php

class Team extends AppModel {

	public $useTable = 'team';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization'
	);

	public $hasMany = array(
		'TeamDevice',
		'TeamRole',
		'TeamFormation',
		'TeamApplication'
	);

	public $hasAndBelongsToMany = array(
        'User' => array(
            'className' => 'User',
            'joinTable' => 'user_team',
            'foreignKey' => 'team_id',
            'associationForeignKey' => 'user_id',
            'conditions' => array(
                'User.is_disabled' => 0
            )
        )
    );

	public $validate = array(
        'organization_id' => array(
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
            'validName' => array(
                'rule' => array('custom','/[A-Za-z0-9-_\. @]{3,}/'),
                'message' => '%%f is limited to letters, numbers and punctuation and must be at least 3 characters long'
            ),
			'checkMultiKeyUniqueness' => array(
				'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
				'message' => 'This %%f is already taken'
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
