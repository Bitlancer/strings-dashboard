<?php

class SudoAttribute extends AppModel {

	public $useTable = 'sudo_attribute';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization',
		'SudoRole' => array(
			'foreignKey' => 'sudo_id'
		)
	);

	public $hasMany = array();

	public $hasAndBelongsToMany = array();

	public $validate = array(
        'sudo_id' => array(
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
                'rule' => array('inList',array('sudoCommand','sudoRunAs','sudoOption')),
                'message' => '%%f is limited to the set (sudoCommand,sudoRunAs,sudoOption)'
            ),
        ),
		'value' => array(
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

		if(isset($this->data['value'])){
			$this->data['value'] = trim($this->data['value']);
		}

		return true;
	}
}
