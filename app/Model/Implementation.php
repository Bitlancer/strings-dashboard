<?php

class Implementation extends AppModel {

	public $useTable = 'implementation';

	public $belongsTo = array(
		'Organization',
		'Provider'
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
				'message' => 'The organization you supplied does not exist'
			)
        ),
		'provider_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Provider id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Provider id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Provider id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The provider you supplied does not exist'
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
				'message' => 'This name is already taken'
			)
        )
    );
	
	public function hasOrganizationConfiguredServiceProvider($organizationId,$service){

		$count = $this->find('count',array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'provider',
                    'alias' => 'Provider',
                    'type' => 'inner',
                    'foreignKey' => false,
                    'conditions' => array('Provider.id = Implementation.provider_id')
                ),
                array(
                    'table' => 'service_provider',
                    'type' => 'inner',
                    'foreignKey' => false,
                    'conditions' => array('Provider.id = service_provider.provider_id')
                ),
                array(
                    'table' => 'service',
                    'alias' => 'Service',
                    'type' => 'inner',
                    'foreignKey' => false,
                    'conditions' => array('service_provider.service_id = Service.id')
                )
            ),
            'conditions' => array(
                'Implementation.organization_id' => $organizationId,
                'Service.name' => $service
            )
        ));

		if($count > 0)
			return true;
		else
			return false;
	}

}
