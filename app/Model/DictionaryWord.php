<?php

class DictionaryWord extends AppModel 
{

    public $useTable = 'dictionary_word';

    public $actsAs = array(
        'OrganizationOwned'
    );

    public $belongsTo = array(
        'Organization',
        'Dictionary'
    );

    public $hasMany = array();

    public $hasAndBelongsToMany = array();

    public $validate = array(
        'organization_id' => array(
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
        'dictionary_id' => array(
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
        'word' => array(
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
                'rule' => array('checkMultiKeyUniqueness',array('word','organization_id')),
                'message' => 'This %%f is already defined'
            )
        ),
        'status' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
            'isBoolean' => array(
                'rule' => 'boolean',
                'message' => '%%f must be a valid boolean'
            )
        ) 
    );
}
