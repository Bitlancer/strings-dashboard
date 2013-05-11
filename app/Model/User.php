<?php

class User extends AppModel {

	public $useTable = 'user';

	public $virtualFields = array(
    	'full_name' => 'CONCAT(User.first_name, " ", User.last_name)',
	);

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization'
	);

	public $hasAndBelongsToMany = array(
		'Team' => array(
			'className' => 'Team',
			'joinTable' => 'user_team',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'team_id'
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
				'message' => 'This username is already taken'
			)
        ),
		'password' => array(
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
			'minLength' => array(
				'rule' => array('minLength', 8),
				'message' => '%%f must be at least 8 characters long'
			)
		),
		'first_name' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty', 
                'message' => '%%f cannot be empty'
            )
		),
		'last_name' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty', 
                'message' => '%%f cannot be empty'
            )
        ),
		'email' => array(
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
			'validEmail' => array(
				'rule' => 'email',
				'message' => '%%f is not a valid email address'
			),
			'checkMultiKeyUniqueness' => array(
                'rule' => array('checkMultiKeyUniqueness',array('email','organization_id')),
                'message' => 'This %%f is already registered to another user'
            )
        ),
		'is_admin' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => '%%f cannot be empty'
			),
			'validValue' => array(
				'rule' => array('inList',array(0,1,'0','1')),
				'message' => '%%f must be a valid boolean'
			)
		),
		'is_disabled' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
            'isBoolean' => array(
                'rule' => 'boolean',
                'message' => '%%f must be a valid boolean'
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
