<?php

class Service extends AppModel {

	public $useTable = 'service';

	public $hasAndBelongsToMany = array(
        'Provider' => array(
            'className' => 'Provider',
            'joinTable' => 'service_provider',
            'foreignKey' => 'service_id',
            'associationForeignKey' => 'provider_id'
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
