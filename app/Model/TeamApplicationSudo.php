<?php

class TeamApplicationSudo extends AppModel
{
	public $useTable = 'team_application_sudo';
	
	public $actsAs = array(
		'OrganizationOwned'
	);
	
	public $belongsTo = array(
        'Organization',
        'TeamApplication',
        'SudoRole' => array(
            'foreignKey' => 'sudo_id'
        )
    );

    public $hasMany = array();
    
    public $hasAndBelongsToMany = array();
    
    public $validate = array(
        'team_application_id' => array(
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
    );
}
