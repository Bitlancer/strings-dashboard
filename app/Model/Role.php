<?php

class Role extends AppModel {

	public $useTable = 'role';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
		'Organization'
	);

	public $hasMany = array(
		'TeamRole' => array(
            'dependent' => true
        ),
        'RoleProfile' => array(
            'dependent' => true
        )
	);

	public $validate = array(
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
			'checkMultiKeyUniqueness' => array(
				'rule' => array('checkMultiKeyUniqueness',array('name','organization_id')),
				'message' => 'This %%f is already taken'
			)
        )
    );

    public function getRoleVariables($roleId){
    
        $role = $this->find('first',array(
            'contain' => array(
                'RoleProfile' => array(
                    'Profile' => array(
                        'ProfileModule' => array(
                            'Module' => array(
                                'ModuleVariable'
                            )
                        )
                    )
                )
            ),
            'conditions' => array(
                'Role.id' => $roleId
            )
        ));

        if(empty($role))
            return false;
        
        $roleVariables = array();
        
        foreach($role['RoleProfile'] as $roleProfile){
            foreach($roleProfile['Profile']['ProfileModule'] as $profileModule){
                
                $module = $profileModule['Module'];
                $moduleId = $module['id'];
                
                if(!count($module['ModuleVariable']))
                    continue;
                
                foreach($module['ModuleVariable'] as $var){
                
                    $varId = $var['id'];
                    
                    if(!isset($roleVariables[$moduleId])){
                        $roleVariables[$moduleId] = array(
                            'id' => $moduleId,
                            'shortName' => $module['short_name'],
                            'name' => $module['name'],
                            'breadcrumb' => array(
                                'role' => $role['Role']['name'],
                                'profile' => $roleProfile['Profile']['name'],
                                'component' => $module['name']
                            ),
                            'variables' => array()
                        );  
                    }
                    
                    if(!isset($roleVariables[$moduleId]['variables'][$varId])){
                        $roleVariables[$moduleId]['variables'][$varId] = $var;
                    }
                }
            }
        }
        
        return $roleVariables;
    }
}
