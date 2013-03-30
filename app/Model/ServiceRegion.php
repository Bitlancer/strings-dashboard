<?php

class ServiceRegion extends AppModel
{
	public $useTable = 'service_region';

	public $validate = array(
        'service_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Service id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Service id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Service id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The service id you supplied does not exist'
            )
        ),
		'region_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Region id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Region id cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => 'Region id must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => 'The region id you supplied does not exist'
            )
        ),
		'endpoint_url' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => 'Endpoint id is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Service id cannot be empty'
            ),
			'isURL' => array(
				'rule' => '


}
