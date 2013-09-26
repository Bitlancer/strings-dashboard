<?php

class Provider extends AppModel {

	public $useTable = 'provider';

    public $belongsTo = array();

    public $hasMany = array(
        'ProviderAttribute'
    );

	public $hasAndBelongsToMany = array(
        'Service' => array(
            'className' => 'Service',
            'joinTable' => 'service_provider',
            'foreignKey' => 'provider_id',
            'associationForeignKey' => 'service_id'
        )
    );

	public $validate = array(
		'service_id' => array(
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
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This %%f is already taken'
			)
        )
    );
}
