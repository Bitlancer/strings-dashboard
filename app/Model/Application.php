<?php

class Application extends AppModel {

	public $useTable = 'application';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization'
	);

	public $hasMany = array(
		'TeamApplication' => array(
            'dependent' => true
        ),
        'ApplicationFormation' => array(
            'dependent' => true
        ),
        'Script' => array(
            'foreignKey' => 'foreign_key_id',
            'conditions' => array(
                'Script.model' => 'application',
            ),
            'dependent' => true
        ),
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
        )
    );
}
