<?php

class Formation extends AppModel {

	public $useTable = 'formation';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization'
	);

	public $hasMany = array(
		'Device',
		'TeamFormation'
	);

	public $hasAndBelongsToMany = array(
        'Application' => array(
            'className' => 'Application',
            'joinTable' => 'application_formation',
            'foreignKey' => 'formation_id',
            'associationForeignKey' => 'application_id'
        ),
		'Team' => array(
            'className' => 'Team',
            'joinTable' => 'team_application',
            'foreignKey' => 'formation_id',
            'associationForiegnKey' => 'team_id'
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
        )
    );
}
