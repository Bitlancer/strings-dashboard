<?php

class Module extends AppModel {

	public $useTable = 'module';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization',
        'ModuleSource',
	);

	public $validate = array(
        'module_source_id' => array(
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
        'short_name' => array(
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
                'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
                'message' => 'This %%f is already taken'
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
				'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
				'message' => 'This %%f is already taken'
			)
        ),
        'reference' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
        ),
        'path' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
        ),
    );
}
