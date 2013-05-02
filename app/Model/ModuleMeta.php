<?php

class ModuleMeta extends AppModel {

    public $useTable = 'module_meta';

    public $belongsTo = array(
        'Module'
    );

    public $validate = array(
        'module_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Module id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Module id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Module id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The module id you supplied does not exist'
            )
        ),
        'var' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Var is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Var cannot be empty'
            ),
			'checkMultiKeyUniqueness' => array(
                'rule' => array('checkMultiKeyUniqueness',array('var','module_id')),
                'message' => 'This var has already been defined for this module'
            )
        ),
        'is_active' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'is_active cannot be empty'
            ),
            'isBoolean' => array(
                'rule' => 'boolean',
                'message' => 'Invalid value for is_active'
            )
        )
    );
}
