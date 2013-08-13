<?php

class Device extends AppModel {
	
	public $useTable = 'device';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization',
        'DeviceType',
		'Implementation',
		'Formation',
		'Role',
        'BlueprintPart'
	);

	public $hasMany = array(
		'DeviceAttribute',
		'TeamDevice'
	);
	
	public $validate = array(
        'device_type_id' => array(
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
		'formation_id' => array(
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
		'role_id' => array(
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
