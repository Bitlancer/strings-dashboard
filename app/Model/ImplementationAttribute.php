<?php

class ImplementationAttribute extends AppModel {

	public $useTable = 'implementation_attribute';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization',
		'Implementation'
	);

	public $hasMany = array();

	public $hasAndBelongsToMany = array();

	public $validate = array(
		'implementation_id' => array(
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

    public function getOverridableAttribute($implementationId,$attributeName){

        $providerAttr = ClassRegistry::init('ProviderAttribute');

        $attr = $this->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'ImplementationAttribute.implementation_id' => $implementationId,
                'ImplementationAttribute.var' => $attributeName
            )
        ));

        if(!empty($attr)){
            $attr = $attr['ImplementationAttribute']['val'];
        }
        else {

            $implementation = $this->findById($implementationId);
            $providerId = $implementation['Implementation']['provider_id'];

            $attr = $providerAttr->find('first',array(
                'contains' => array(),
                'conditions' => array(
                    'ProviderAttribute.provider_id' => $providerId,
                    'ProviderAttribute.var' => $attributeName
                )
            ));

            if(empty($attr))
                return false;
            else {
                $attr = $attr['ProviderAttribute']['val'];
            }
        }

        return $attr;
    }
}
