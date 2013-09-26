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
        'UserTeam' => array(
            'dependent' => true
        ),
		'TeamDevice' => array(
            'dependent' => true
        ),
		'TeamRole' => array(
            'dependent' => true
        ),
		'TeamFormation' => array(
            'dependent' => true
        ),
		'TeamApplication' => array(
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
                'message' => '%%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
            'validName' => array(
                'rule' => AppModel::VALID_MODEL_NAME_REGEX,
                'message' => AppModel::VALID_MODEL_NAME_MSG
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
