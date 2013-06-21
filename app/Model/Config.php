<?php

class Config extends AppModel {

	public $useTable = 'config';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization'
	);

	public $hasMany = array();

	public $hasAndBelongsToMany = array();

	public $validate = array(
        'var' => array(
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
                'rule' => array('checkMultiKeyUniqueness',array('var','organization_id')),
                'message' => 'This %%f has already been defined'
            )
        )
    );
}
