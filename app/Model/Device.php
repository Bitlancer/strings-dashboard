<?php

class Device extends AppModel {
	
	public $useTable = 'device';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization',
        'DeviceType',
		'Implementation',
		'Formation',
		'Role',
        'BlueprintPart'
	);

	public $hasMany = array(
		'DeviceAttribute',
		'TeamDevice',
        'DeviceDns'
	);
	
	public $validate = array(
        'device_type_id' => array(
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
		'formation_id' => array(
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
		'role_id' => array(
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
			'checkMultiKeyUniqueness' => array(
                'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
                'message' => 'This %%f is already taken'
            )
		),
        'status' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ), 
            'validStatus' => array(
                'rule' => array('inList',array('building','resizing','active','deleting','error')),
                'message' => '%%f is an invalid status'
            )
        )
	);

    public function getCustomerDatacenter($deviceId){

        $implementationAttr = ClassRegistry::init('ImplementationAttribute');

        $regionId = $this->DeviceAttribute->findByVar('implementation.region_id');
        $regionId = $regionId['DeviceAttribute']['val'];
        if(empty($regionId))
            throw new InternalErrorException("Attribute implementation.region_id is not defined.");

        $regions = $implementationAttr->getOverridableAttribute('regions');
        if(empty($regions))
            throw new InternalErrorException("Attribute regions is not defined.");
        $regions = json_decode($regions,true);
        $regions = Hash::combine($regions,'{n}.id','{n}');

        if(!isset($regions[$regionId]))
            throw new InternalErrorException("Unrecognized region id.");

        return $regions[$regionId]['name'];
    }

    public function beforeSave($options = array()){

        $data = $this->data[$this->alias];

        if(isset($data['name'])){
            $this->data[$this->alias]['name'] = strtolower($data['name']);
        }

        return true;
    }

    public function afterFind($results,$primary = false){

        //ucwords(name)
        if(isset($results['name'])){
            $results['name'] = ucwords($results['name']);
        }
        else {
            foreach($results as $key => $result){
                if(isset($result[$this->alias]['name'])) {
                    $name = ucwords($result[$this->alias]['name']);
                    $results[$key][$this->alias]['name'] = $name;
                }
            }
        }
        return $results;
    }
}
