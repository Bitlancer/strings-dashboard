<?php

class Implementation extends AppModel {

	public $useTable = 'implementation';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization',
		'Provider'
	);

    public $hasMany = array(
        'ImplementationAttribute'
    );

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
		'provider_id' => array(
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
                'rule' => array('custom','/[A-Za-z0-9-_\. @]{3,}/'),
                'message' => '%%f is limited to letters, numbers and punctuation and must be at least 3 characters long'
            ),
			'checkMultiKeyUniqueness' => array(
				'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
				'message' => 'This %%f is already taken'
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

    public function getFlavorDescription($implementationId,$flavorId){

        $flavorDescrsAttribute = $this->getOverridableAttribute($implementationId,'flavor_descriptions');
        if($flavorDescrsAttribute !== false){

            $flavorDescrs = $flavorDescrsAttribute['val'];
            $flavorDescrs = json_decode($flavorDescrs,true);

            foreach($flavorDescrs as $flavor){
                if($flavor['id'] == $flavorId)
                    return $flavor['description']; 
            }
        }

        return 'Unknown';
    }

    public function getRegions($implementationId){

        $regionsAttribute = $this->getOverridableAttribute($implementationId,'regions');
        if($regionsAttribute === false)
            return false;

        $regions = $regionsAttribute['val'];

        $regions = json_decode($regions,true);
        return $regions;
    }

    /**
     * Certain provider attributes should be overridable to allow
     * customers to change names, etc. This method will first try to
     * pull an attribute from ImplementationAttribute. If it does
     * not exist it will pull in the default from ProviderAttribute.
     *
     * @param string $attributeVar The name of the attribute
     * @return mixed[] The attribute val or false if not found
     */
    private function getOverridableAttribute($implementationId, $attributeVar){

        if(!$this->exists($implementationId))
            return false;

        $attribute = $this->ImplementationAttribute->find('first',array(
            'fields' => array(
                'ImplementationAttribute.val',
            ),
            'conditions' => array(
                'ImplementationAttribute.implementation_id' => $implementationId,
                'ImplementationAttribute.var' => $attributeVar
            )
        ));

        if(!empty($attribute)){
            $attribute = $attribute['ImplementationAttribute'];
        }
        else {

            $implementation = $this->findById($implementationId);
            $providerId = $implementation['Implementation']['provider_id'];

            $providerAttribute = ClassRegistry::init('ProviderAttribute');
            $attribute = $providerAttribute->find('first',array(
                'conditions' => array(
                    'ProviderAttribute.provider_id' => $providerId,
                    'ProviderAttribute.var' => $attributeVar
                )
            ));

            if(empty($attribute))
                return false;
            else {
                $attribute = $attribute['ProviderAttribute'];
            }
        }

        return $attribute;
    }

}
