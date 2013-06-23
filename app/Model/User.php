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

    public $hasMany = array(
        'UserTeam' => array(
            'dependent' => true
        ),
        'UserAttribute' => array(
            'dependent' => true
        ),
        'UserKey' => array(
            'dependent' => true
        )
    );

	public $hasAndBelongsToMany = array();

	public $validate = array(
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
            $this->data['User']['password'] = Security::hash($this->data['User']['password'],'sha1',false);

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

    public function reserveNextPosixUid(){

        $config = ClassRegistry::init('Config');

        $nextUid = false;
        
        $attr = $config->findByVar('posix.next_uid');
        if(!empty($attr)){
            $attrId = $attr['Config']['id'];
            $nextUid = $attr['Config']['val'];

            $config->id = $attrId;
            if(!$config->saveField('val',$nextUid+1)){
                $nextUid = false;
            }
        }

        return $nextUid;
    }

    public function setDefaultPosixAttributes($userId){

        $config = ClassRegistry::init('Config');

        $uid = $this->reserveNextPosixUid();
        if($uid === false)
            return false;
        $gid = $uid;

        $shell = $config->findByVar('posix.default_shell');
        if($shell === false)
            return false;
        $shell = $shell['Config']['val'];

        $attributes = array(
            array(
                'user_id' => $userId,
                'var' => 'posix.uid',
                'val' => $uid,
            ),
            array(
                'user_id' => $userId,
                'var' => 'posix.gid',
                'val' => $gid
            ),
            array(
                'user_id' => $userId,
                'var' => 'posix.shell',
                'val' => $shell
            )
        );

        return $this->UserAttribute->saveMany($attributes);
    }


    /**
     * Set a custom audit description
     * This method is called by the auditable behavior
     *
     * @param $data mixed[] Audit record data
     * @param $operation string (create|update|delete)
     * @param $updates mixed[] List of AuditDelta records - will only be set if $operation == update
     * @return string Audit description
     */
    public function _auditDescription($audit,$operation,$auditDeltas=array()){

        if($operation == 'update'){

        }

        return null;
    }
}
