<?php

class UserKey extends AppModel {

	public $useTable = 'user_key';

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
			'checkMultiKeyUniqueness' => array(
				'rule' => array('checkMultiKeyUniqueness',array('name','user_id')),
				'message' => 'This %%f has already been taken'
			)
        ),
        'public_key' => array(
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
        ),
    );

    public function beforeSave($options=array()){

        if(!parent::beforeSave($options))
            return false;

        //Remove description from public key and add user supplied name
        if(isset($this->data['UserKey']['name']) && isset($this->data['UserKey']['public_key'])){

            $name = $this->data['UserKey']['name'];
            $publicKey = $this->data['UserKey']['public_key'];

            $startOfKeyDescr = strrpos($publicKey,' ');
            //> 8 since key starts with ssh-rsa[space]
            if($startOfKeyDescr !== false && $startOfKeyDescr > 8){
                $publicKey = substr($publicKey,0,$startOfKeyDescr);
            }

            //Disabled b/c I am worried about what characters the name might contain
            //$publicKey .= " " . $name;

            $this->data['UserKey']['public_key'] = $publicKey;
        }

        return true;
    }
}
