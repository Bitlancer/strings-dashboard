<?php

class Formation extends AppModel {

	public $useTable = 'formation';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization',
        'Blueprint'
	);

	public $hasMany = array(
		'Device',
		'TeamFormation' => array(
            'dependent' => true
        ),
        'ApplicationFormation' => array(
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
        'status' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
            'validStatus' => array(
                'rule' => array('inList',array('building','resizing','active','deleting','error')),
                'message' => '%%f is an invalid status'
            )
        )
    );
}
