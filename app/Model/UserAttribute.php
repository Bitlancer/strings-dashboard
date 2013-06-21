<?php

class UserAttribute extends AppModel {

	public $useTable = 'user_attribute';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization',
		'User'
	);

	public $hasMany = array();

	public $hasAndBelongsToMany = array();

	public $validate = array(
		'user_id' => array(
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
        'var' => array(
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
			'checkMultiKeyUniqueness' => array(
				'rule' => array('checkMultiKeyUniqueness',array('var','user_id')),
				'message' => 'This %%f has already been defined'
			)
        )
    );

    public function saveAttribute($user,$var,$val){

        if(!isset($user['User']) || !isset($user['User']['id']) || !isset($user['User']['organization_id']))
            throw new InvalidArgumentException('User array must contain id and organization_id');

        if(empty($var))
            throw new InvalidArgumentException('Var cannot be empty');
        
        $attr = $this->find('first',array(
            'fields' => array(
                'id','val'
            ),
            'conditions' => array(
                'user_id' => $user['User']['id'],
                'var' => $var
            ),
        ));

        if(empty($attr)){

            $userAttr = array(
                'UserAttribute' => array(
                    'organization_id' => $user['User']['organization_id'],
                    'user_id' => $user['User']['id'],
                    'var' => $var,
                    'val' => $val
                )
            );

           return $this->save($userAttr);
        }
        else {

            if($attr['UserAttribute']['val'] == $val)
                return true;

            $this->id = $attr['UserAttribute']['id'];
            return $this->save(array(
                'val' => $val
            ));
        }
    }
}
