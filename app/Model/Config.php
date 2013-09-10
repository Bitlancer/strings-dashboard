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

    public function getDnsSuffixes(){

        $internalDnsSuffix = $this->findByVar('dns.internal.domain');
        if(empty($internalDnsSuffix))
            throw new InternalErrorException('The dns.internal.domain configuration has not been defined');
        $internalDnsSuffix = $internalDnsSuffix['Config']['val'];

        $externalDnsSuffix = $this->findByVar('dns.external.domain');
        if(empty($externalDnsSuffix))
            throw new InternalErrorException('The dns.external.domain configuration has not been defined');
        $externalDnsSuffix = $externalDnsSuffix['Config']['val'];

        return array($internalDnsSuffix,$externalDnsSuffix);
    }
}
