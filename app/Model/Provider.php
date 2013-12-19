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

    public function getLoadbalancerAttributes($providerId){

        $lbAttrVars = array(
            'load_balancers.virtual_ip_types',
            'load_balancers.protocols',
            'load_balancers.algorithms',
            'load_balancers.session_persistence_options'
        );

        $lbAttrs = $this->ProviderAttribute->find('all',array(
            'contain' => array(),
            'conditions' => array(
                'ProviderAttribute.var' => $lbAttrVars,
                'ProviderAttribute.provider_id' => $providerId
            )
        ));

        $lbAttrs = Hash::combine($lbAttrs,'{n}.ProviderAttribute.var','{n}.ProviderAttribute.val');

        //Verify attributes were retrieved
        foreach($lbAttrVars as $var){
            if(!isset($lbAttrs[$var]))
                throw new InternalErrorException("Load-balancer attribute $var has not been defined for this provider");
        }

        return $lbAttrs;
    }
}
