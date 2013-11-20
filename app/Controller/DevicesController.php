<?php

class DevicesController extends AppController
{

    public $components = array(
        'FormationsAndDevices'
    );

    /**
     * Authorization logic
     */
    public function isAuthorized($user){

        if(parent::isAuthorized($user))
            return true;

        switch($this->action){
            case 'index':
            case 'view':
                return true;
        }

        return false;
    }

	/**
     * Home screen containing list of devices and create device CTA
     */
    public function index() {

		//Verify this organization has setup one or more infrastructure providers
		$isInfraProviderConfigured = $this->Device->Implementation->hasOrganizationConfiguredServiceProvider($this->Auth->user('organization_id'),'infrastructure');
		if(!$isInfraProviderConfigured){
			$this->setFlash('Please setup an infrastructure provider <a href="#">here</a>.');
		}

        $this->DataTables->setColumns(array(
             'Name' => array(
                'model' => 'Device',
                'column' => 'name'
            ),
            'Formation' => array(
                'model' => 'Formation',
                'column' => 'name'
            ),
            'Role' => array(
                'model' => 'Role',
                'column' => 'name'
            )
        ));

        if($this->request->isAjax()){

            $this->DataTables->process(
                array(
                    'contain' => array(
                        'DeviceType',
                        'Formation',
                        'Role'
                    ),
                    'fields' => array(
                        'Device.*','DeviceType.*',
                        'Formation.*','Role.name'
                    )
                )
            );
 

            $this->set(array(
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'createCTADisabled' => !$isInfraProviderConfigured || !$this->Auth->User('is_admin'),
            ));
        }
    }

    public function view($id=null){

        $device = $this->Device->find('first',array(
            'contain' => array(
                'DeviceType',
                'Implementation' => array(
                    'ImplementationAttribute',
                ),
                'Formation',
                'Role',
                'DeviceAttribute'
            ),
            'conditions' => array(
                'Device.id' => $id
            )
        ));

        if(empty($device))
            throw new NotFoundException('Device does not exist.');

        $deviceType = $device['DeviceType']['name'];

        if($deviceType == 'instance'){
            $this->_viewInstance($device);
        }
        elseif($deviceType == 'load-balancer'){
            $this->_viewLoadBalancer($device); 
        }
        else {
            throw new InternalErrorException('Unknown device type.');
        }
    }

    private function _viewInstance($device){

        //Build re-indexed attributes arrays
        $deviceAttributes = Hash::combine($device['DeviceAttribute'],'{n}.var','{n}.val');
        $implementationAttributes  = Hash::combine($device['Implementation']['ImplementationAttribute'],'{n}.var','{n}.val');

        $implementationId = $device['Implementation']['id'];

        //Provider info
        $providerInfo = array(
            'Provider' => $device['Implementation']['name'],
            'Datacenter' => $this->Device->Implementation->getRegionName($implementationId,$deviceAttributes['implementation.region_id']),
            'Image' => 'Default',
            'Flavor' => $this->Device->Implementation->getFlavorDescription($implementationId,$deviceAttributes['implementation.flavor_id'])
        );

        //Address info
        $deviceAddresses = array();
        foreach($deviceAttributes as $var => $val){
            if(strpos($var,'implementation.address') === 0){

                list($i,$a,$network,$version) = explode('.',$var); 

                $descr = ucfirst($network) . " IPv" . $version; 
                $address = $val;

                $deviceAddresses[] = array($descr,$address);
            }
        }

        $this->set(array(
            'device' => $device,
            'providerInfo' => $providerInfo,
            'deviceAddresses' => $deviceAddresses
        ));
    }

    private function _viewLoadBalancer($device){

        //Build re-indexed attributes arrays
        $deviceAttributes = Hash::combine($device['DeviceAttribute'],'{n}.var','{n}.val');
        $implementationAttributes  = Hash::combine($device['Implementation']['ImplementationAttribute'],'{n}.var','{n}.val');

        $implementationId = $device['Implementation']['id'];

        //Provider info
        $providerInfo = array(
            'Provider' => $device['Implementation']['name'],
            'Datacenter' => $this->Device->Implementation->getRegionName($implementationId,$deviceAttributes['implementation.region_id']),
            'Protocol' => Inflector::humanize(strtolower($deviceAttributes['implementation.protocol'])),
            'Port' => $deviceAttributes['implementation.port'],
            'Algorithm' => Inflector::humanize(strtolower($deviceAttributes['implementation.algorithm'])),
        );

        //Device addresses
        $deviceAddresses = array();

        $this->set(array(
            'device' => $device,
            'providerInfo' => $providerInfo,
            'deviceAddresses' => $deviceAddresses
        ));
    }

    public function create($id=null){

        $formations = $this->Device->Formation->find('all',array(
            'contain' => array(),
            'fields' => array('Formation.*')
        ));

        $this->set('formations', $formations);
    }

    public function resize($id=null){

        $this->loadModel('QueueJob');

        $device = $this->Device->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Device.id' => $id,
            )
        ));

        if(empty($device)){
            $this->setFlash('Device does not exist.');
            $this->redirect(array('action' => 'index'));
        }

        $this->redirectIfNotActive($device);

        //Get a list of flavors
        $flavors = $this->Device->Implementation->getFlavors($device['Device']['implementation_id']);
        $flavors = Hash::combine($flavors,'{n}.id','{n}');

        //Remove the devices current flavor from the list
        $deviceFlavor = $this->Device->DeviceAttribute->findByVar('implementation.flavor_id');
        $deviceFlavorId = $deviceFlavor['DeviceAttribute']['val'];
        unset($flavors[$deviceFlavorId]);

        if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            if(!isset($this->request->data['flavor']) || empty($this->request->data['flavor'])){
                $isError = true;
                $message = 'Please select a size.';
            }
            else {
                
                $deviceId = $device['Device']['id']; 
                $flavorId = $this->request->data['flavor'];

                //Validate flavor id
                $flavorIds = array_keys($flavors); 
                if(!in_array($flavorId,$flavorIds)){
                    $isError = true;
                    $message = 'Invalid size selected.';
                }
                else {

                    //Add Q job to resize instance
                    if(!$this->QueueJob->addjob(STRINGS_API_URL . "/Instances/resize/$deviceId/$flavorId")){
                        $isError = true;
                        $message = 'Failed to create a job to resize instance.';
                    }
                    else {
                        $this->Device->id = $deviceId;
                        $this->Device->saveField('status','resizing',true);
                        $message = "Resize initiated for device {$device['Device']['name']}.";
                    }
                }
            }

            if($isError){
                $response = array(
                    'isError' => $isError,
                    'message' => __($message)
                );
            }
            else {
                $this->setFlash($message,'success');
                $response = array(
                    'redirectUri' => $this->referer(array('action' => 'index'))
                );
            }

            echo json_encode($response);
        }
        else {
            $this->set(array(
                'device' => $device,
                'flavors' => $flavors
            ));
        }
    }

    public function configure($deviceId){

        $device = $this->Device->find('first',array(
            'contain' => array(
                'DeviceType',
                'DeviceAttribute' => array(
                    'conditions' => array(
                        'var' => 'dns.external.fqdn'
                    )
                ),
                'Implementation' => array(
                    'Provider'
                ),
            ),
            'conditions' => array(
                'Device.id' => $deviceId
            )
        ));

        if(empty($device))
            throw new NotFoundException('Device does not exist.');

        $deviceType = $device['DeviceType']['name'];

        if($deviceType == 'instance'){
            $this->_configureInstance($device);
        }
        elseif($deviceType == 'load-balancer'){
            $this->_configureLoadBalancer($device);
        }
        else {
            throw new InternalErrorException('Unknown device type.');
        }
    }

    private function _configureInstance($device){

        $this->loadModel('HieraVariable');

        $deviceId = $device['Device']['id'];
        $roleId = $device['Device']['role_id'];
        $deviceFqdn = $device['DeviceAttribute'][0]['val'];
        $hieraKey = "fqdn/$deviceFqdn";

        //Get role variables
        $variables = $this->Device->Role->getRoleVariables($roleId);

        //Get currently set Hiera variables
        $existingVarVals = $this->HieraVariable->find('all',array(
            'contain' => array(),
            'conditions' => array(
                'hiera_key' => $hieraKey
            )
        ));
        $existingVarVals = Hash::combine($existingVarVals,'{n}.HieraVariable.var','{n}.HieraVariable.val');

        //Merge existing variables with possible variables
        //Overwrite default_value with existing variable value
        foreach($variables as $moduleId => $module){
            foreach($module['variables'] as $index => $variable){
                $var = $variable['var'];
                if(isset($existingVarVals[$var])){
                    $variables[$moduleId]['variables'][$index]['default_value'] = 
                        $existingVarVals[$var]; 
                }
            }
        }

        $errors = array();

        if($this->request->is('post')){

            //Validate new variable values
            $input = $this->request->data['variables'];
            list($newVariables,$errors) = 
                $this->HieraVariable->parseAndValidateDeviceVariables($variables,$input,$hieraKey);

            if(!empty($errors))
                $this->setFlash('One or more variables is invalid or missing.');

            //Store new variables
            if(empty($errors)){
                
                //Convert to key-value (var-value) array so we can diff
                //against the existing variables
                $newVarVals = Hash::combine($newVariables,'{n}.var','{n}.val');

                //Diff
                $diff = array_diff_assoc($newVarVals,$existingVarVals);

                //Apply changes
                $allChangesApplied = true;
                foreach($diff as $var => $val){

                    $val = $this->HieraVariable->escapeValue($val);
                    $result = $this->HieraVariable->updateAll(
                        array(
                            'HieraVariable.val' => $val
                        ),
                        array(
                            'HieraVariable.organization_id' => $this->Auth->User('organization_id'),
                            'HieraVariable.hiera_key' => $hieraKey,
                            'HieraVariable.var' => $var
                        )
                    );

                    if(!$result){
                        $allChangesApplied = false;
                        break;
                    }
                }

                if(!$allChangesApplied){
                    $this->setFlash('Failed to apply all variable changes.');
                    $this->sLog('Failed to apply all variable cahnges. ' .
                        $this->HieraVariable->validationErrorsAsString(true));
                }
                else {
                    $this->setFlash('Configuration changes applied successfully.','success');
                    $this->redirect("/Devices/view/$deviceId");
                }
            }
        }

        $this->set(array(
            'device' => $device,
            'variables' => $variables,
            'errors' => $errors
        ));

        $this->render('configure_instannce');
    }

    private function _configureLoadBalancer($device){

        $this->loadModel('QueueJob');

        $formData = array();

        $deviceId = $device['Device']['id'];
        $implementationId = $device['Implementation']['id'];

        $formData['errors'] = array();
        $formData['device'] = $device;

        //Get load-balancer form data
        $formData = array_merge(
            $formData,
            $this->FormationsAndDevices->_getLoadBalancerFormData($implementationId)
        );

        //Get load-balancer attributes
        list($virtualIpType,$protocolName,$port,$algorithmName) =
            $this->_getLoadBalancerAttributes($device['Device']['id']);

        
        if($this->request->is('post')){

            list($newAttrs,$errors) = $this->FormationsAndDevices->_parseAndValidateLoadBalancer(
                array(),
                $this->request->data['Device'],
                $implementationId
            );
            $newAttrs = $newAttrs['DeviceAttribute'];

            if(!empty($errors)){
                $formData['errors'] = $errors;
            }
            else {
                foreach($newAttrs as $attr){
                    $result = $this->Device->DeviceAttribute->updateAll(
                        array(
                            'DeviceAttribute.val' => $this->Device->escapeValue($attr['val']),
                        ),
                        array(
                            'DeviceAttribute.var' => $attr['var'],
                            'DeviceAttribute.device_id' => $deviceId,
                            'DeviceAttribute.organization_id' => $this->Auth->User('organization_id')
                        )
                    );
                    if($result == false){
                        $formData['errors'][] = $this->Device->DeviceAttribute->validationErrorsAsString();
                    }
                }
                if(empty($formData['errors'])) {

                    if($this->QueueJob->addJob(STRINGS_API_URL . "/LoadBalancers/update/$deviceId")){
                        $this->Device->id = $deviceId;
                        $this->Device->saveField('status','building');
                        $this->redirect("/Devices/view/$deviceId");
                    }
                    else {
                        $formData['errors'][] = 'Error encountered while scheduling a job to update this load-balancer.'; 
                    }
                }
            }
        }
        else {
            $this->request->data = array(
                'Device' => array(
                    'virtualIpType' => $virtualIpType,
                    'protocol' => $protocolName,
                    'port' => $port,
                    'algorithm' => $algorithmName
                )
            );
        }

        $this->set($formData);

        $this->render('configure_loadbalancer');
    }

    private function _getLoadBalancerAttributes($deviceId){

        $attrVars = array(
            'implementation.virtual_ip_type',
            'implementation.protocol',
            'implementation.port',
            'implementation.algorithm'
        );

        $attrs = $this->Device->DeviceAttribute->find('all',array(
            'contain' => array(),
            'conditions' => array(
                'DeviceAttribute.var' => $attrVars,
                'DeviceAttribute.device_id' => $deviceId
            )
        ));

        $attrs = Hash::combine($attrs,'{n}.DeviceAttribute.var','{n}.DeviceAttribute.val');

        foreach($attrVars as $var)
            if(!isset($attrs[$var]))
                throw new InternalErrorException("Load-balancer attribute $var is not defined for device $deviceId");

        return array(
            $attrs['implementation.virtual_ip_type'],
            $attrs['implementation.protocol'],
            $attrs['implementation.port'],
            $attrs['implementation.algorithm']
        );
    }

    protected function redirectIfNotActive($device){
    
        if($device['Device']['status'] != 'active'){
            $this->setFlash('This device is currently undergoing another operation. It cannot be modified until the previous operation completes.');
            $this->redirect($this->referer(array('action' => 'index')));
        }
    }
}
