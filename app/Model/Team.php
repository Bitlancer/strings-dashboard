<?php

class Team extends AppModel {

	public $useTable = 'team';

	public $belongsTo = array(
		'Organization'
	);

	public $validate = array(
        'organization_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Organization id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Organization id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Organization id must be an integer'
            ),
			'validForeignKey' => array(
				'rule' => array('isValidForeignKey'),
				'message' => 'The organization id you supplied does not exist'
			)
        ),
        'name' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Name is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Name cannot be empty'
            ),
            'validName' => array(
                'rule' => array('custom','/[A-Za-z0-9-_\. @]{3,}/'),
                'message' => 'Name is limited to letters, numbers and punctuation and must be at least 3 characters long'
            ),
			'checkMultiKeyUniqueness' => array(
				'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
				'message' => 'A team with this name already exists within your organization.'
			)
        )
    );

    public function beforeSave($options = array()) {

		if(isset($this->data['Team']['name']))
            $this->data['Team']['name'] = ucwords(strtolower($this->data['Team']['name']));

        return true;
    }
}
