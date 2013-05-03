<?php

class User extends AppModel {

	public $useTable = 'user';

	public $virtualFields = array(
    	'full_name' => 'CONCAT(User.first_name, " ", User.last_name)',
	);

	public $belongsTo = array(
		'Organization'
	);

	public $hasAndBelongsToMany = array(
		'Team' => array(
			'className' => 'Team',
			'joinTable' => 'user_team',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'team_id',
			'conditions' => array(
				'Team.is_disabled' => 0
			)
		)
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
        'name' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Username is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Username cannot be empty'
            ),
            'validUsername' => array(
                'rule' => array('custom','/[A-Za-z0-9-_\. @]{3,}/'),
                'message' => 'Username is limited to letters,numbers and punctuation and must be at least 3 characters long'
            ),
			'checkMultiKeyUniqueness' => array(
				'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
				'message' => 'A user with this username already exists within your organization'
			)
        ),
		'password' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Password is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Password cannot be empty'
            ),
			'minLength' => array(
				'rule' => array('minLength', 8),
				'message' => 'Password must be at least 8 characters long'
			)
		),
		'first_name' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'First name is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty', 
                'message' => 'First name cannot be empty'
            )
		),
		'last_name' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Last name is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty', 
                'message' => 'Last name cannot be empty'
            )
        ),
		'email' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Email is required'
            ),  
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Email cannot be empty'
            ),
			'validEmail' => array(
				'rule' => 'email',
				'message' => 'The supplied email address is not valid'
			),
			'checkMultiKeyUniqueness' => array(
                'rule' => array('checkMultiKeyUniqueness',array('email','organization_id')),
                'message' => 'A user with this email address already exists within your organization'
            )
        ),
		'is_admin' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'is_admin cannot be empty'
			),
			'validValue' => array(
				'rule' => array('inList',array(0,1,'0','1')),
				'message' => 'is_admin must be 0 or 1'
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

    public function beforeSave($options = array()) {

        if(isset($this->data['User']['password']))
            $this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);

		if(isset($this->data['User']['name']))
            $this->data['User']['name'] = strtolower($this->data['User']['name']);

		if(isset($this->data['User']['first_name']))
			$this->data['User']['first_name'] = ucfirst(strtolower($this->data['User']['first_name']));

		if(isset($this->data['User']['last_name']))
            $this->data['User']['last_name'] = ucfirst(strtolower($this->data['User']['last_name']));

		if(isset($this->data['User']['email']))
            $this->data['User']['email'] = strtolower($this->data['User']['email']);

        return true;
    }
}
