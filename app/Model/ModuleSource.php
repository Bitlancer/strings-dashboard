<?php

class ModuleSource extends AppModel {

	public $useTable = 'module_source';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization'
	);

    public $hasMany = array(
        'Module',
    );

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
			'checkMultiKeyUniqueness' => array(
				'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
				'message' => 'This %%f is already taken'
			)
        ),
        'type' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
            'validType' => array(
                'rule' => array('inList',array('git','forge')),
                'message' => '%%f is an invalid status'
            )
        ),
        'url' => array(
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
            'validUrl' => array(
                'rule' => 'url',
                'message' => '%%f is not a valid Url'
            )
        ),
    );
}
