<?php

class Profile extends AppModel {

	public $useTable = 'profile';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization'
	);

    public $hasMany = array(
        'RoleProfile' => array(
            'dependent' => true
        ),
        'ProfileModule' => array(
            'dependent' => true
        )
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
        )
    );
}
