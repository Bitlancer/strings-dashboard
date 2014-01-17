<?php

class DevicesController extends AppController
{

    public $components = array(
        'FormationsAndDevices',
        'StringsApiServiceCatelog'
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
		$hasInfraProvider = $this->Device->Implementation->hasServiceProvider(
            $this->Auth->user('organization_id'),
            'infrastructure'
        );
		if(!$hasInfraProvider){
			$this->setFlash('Please setup an infrastructure provider.');
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
                'createCTADisabled' => !$hasInfraProvider || !$this->Auth->User('is_admin'),
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

        //If device is in the verify_resize state, prompt the user
        //with a confirmation dialog
        if($device['Device']['status'] == 'verify_resize')
            $this->promptUserForResizeConfirmation($device);

        //Build re-indexed attributes arrays
        $deviceAttributes = Hash::combine($device['DeviceAttribute'],'{n}.var','{n}.val');
        $implementationAttributes  = Hash::combine($device['Implementation']['ImplementationAttribute'],'{n}.var','{n}.val');

        $implementationId = $device['Implementation']['id'];

        //Device info
        $deviceInfo = array(
            'name' => $device['Device']['name'],
            'status' => $this->userFriendlyDeviceStatus($device['Device']['status']),
            'role' => $device['Role']['name'],
            'formation' => $device['Formation']['name'],
            'created' => $device['Device']['created']
        );

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
            'deviceInfo' => $deviceInfo,
            'providerInfo' => $providerInfo,
            'deviceAddresses' => $deviceAddresses
        ));
    }

    private function userFriendlyDeviceStatus($status){

        return Inflector::humanize($status);    
    } 

    private function promptUserForResizeConfirmation($device){

        $deviceId = $device['Device']['id'];

        $confirmLink = "<a href=\"/Devices/confirmResize/$deviceId\">confirm</a>";
        $revertLink = "<a href=\"/Devices/revertResize/$deviceId\">revert</a>";

        $msg = "This device has recently undergone a resize operation. " .
            "If the device is in working order please click $confirmLink, " .
            "to complete the resize operation. Click $revertLink to restore " .
            "the device to its previous size.";

        $this->setFlash($msg, 'warning');
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
                    $apiUrl = $this->StringsApiServiceCatelog->getUrl(
                        'infrastructure',
                        "/Instances/resize/$deviceId/$flavorId"
                    );
                    if(!$this->QueueJob->addjob($apiUrl)){
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

    public function confirmResize($deviceId){

        $this->loadModel('QueueJob');

        $device = $this->Device->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Device.id' => $deviceId,
            )
        ));

        if(empty($device)){
            $this->setFlash('Device does not exist.');
        }
        elseif($device['Device']['status'] != 'verify_resize'){
            $this->setFlash('This operation is not valid for this device at this time.');
        }
        else {
            $apiUrl = $this->StringsApiServiceCatelog->getUrl(
                'infrastructure',
                "/Instances/confirmResize/$deviceId"
            );
            $result = $this->QueueJob->addJob($apiUrl);
            if(!$result){
                $this->setFlash('Failed to initiate a resize confirmation.');
            }
            else {
                $this->Device->id = $deviceId;
                $this->Device->saveField('status','resizing',true);
                $this->setFlash('Initiating completion of the resize operation.', 'success');
            }
        }

        $this->redirect($this->referer(array('action' => 'index')));
    }

    public function revertResize($deviceId){

        $this->loadModel('QueueJob');

        $device = $this->Device->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Device.id' => $deviceId,
            )
        ));

        if(empty($device)){
            $this->setFlash('Device does not exist.');
        }
        elseif($device['Device']['status'] != 'verify_resize'){
            $this->setFlash('This operation is not valid for this device at this time.');
        }
        else {
            $apiUrl = $this->StringsApiServiceCatelog->getUrl(
                'infrastructure',
                "/Instances/revertResize/$deviceId"
            );
            $result = $this->QueueJob->addJob($apiUrl);
            if(!$result){
                $this->setFlash('Failed to initiate the revert opreation.');
            }
            else {
                $this->Device->id = $deviceId;
                $this->Device->saveField('status','revert_resize',true);
                $this->setFlash('Initiating the revert operation.', 'success');
            }
        }

        $this->redirect($this->referer(array('action' => 'index')));

    }

    public function configure($deviceId){

        $device = $this->Device->find('first',array(
            'contain' => array(
                'DeviceType',
                'DeviceAttribute',
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

        //Index attributes
        $deviceAttrs = Hash::combine($device['DeviceAttribute'],'{n}.var','{n}');

        $deviceId = $device['Device']['id'];
        $roleId = $device['Device']['role_id'];
        $deviceFqdn = $deviceAttrs['dns.external.fqdn']['val'];
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
            list($errors, $newVariables) = 
                $this->FormationsAndDevices->parseAndValidateInstanceVariables($device,$input);

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

        $this->render('configure_instance');
    }

    private function _configureLoadBalancer($device){

        $providerName = $device['Implementation']['Provider']['name'];

        if($providerName == 'Rackspace'){
            $this->_configureRackspaceLoadBalancer($device);        
        }
        else {
            throw new Exception('Unexpected provider');
        }
    }

    private function _configureRackspaceLoadBalancer($device){

        $this->loadModel('QueueJob');

        $viewData = array(
            'device' => $device
        );

        $deviceId = $device['Device']['id'];
        $implementationId = $device['Implementation']['id'];
        $providerId = $device['Implementation']['provider_id'];

         //Editable attributes
        $editableAttrs = array(
            'protocol' => 'implementation.protocol',
            'port' => 'implementation.port',
            'algorithm' => 'implementation.algorithm',
            'sessionPersistence' => 'implementation.session_persistence' 
        );

        //Reindex attributes by var
        $deviceAttrs = Hash::combine($device['DeviceAttribute'],'{n}.var','{n}');

        foreach($editableAttrs as $attrName => $attrVar){
            $viewData[$attrName] = $deviceAttrs[$attrVar]['val'];
        }

        //Get load-balancer form data
        $viewData = array_merge($viewData, $this->FormationsAndDevices->getLoadBalancerFormData($implementationId));

        $this->set($viewData);

        if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";
            $redirectUri = null;

            //Validate input
            foreach($editableAttrs as $attrName => $attrVar){
                $attrVal = $this->request->data($attrName);
                list($valid,
                    $errMsg) = $this->FormationsAndDevices->validateRackspaceLoadBalancerAttribute($providerId, $attrName, $attrVal);
                if(!$valid) {
                    $isError = true;
                    $message = $errMsg;
                }
            }

            //Update attributes and create q job
            if(!$isError){

                $updatedAttrs = array();

                //Update attributes
                foreach($editableAttrs as $attrName => $attrVar){
                    $currentAttr = $deviceAttrs[$attrVar];
                    $inputAttrVal = $this->request->data($attrName);
                    if($currentAttr['val'] != $inputAttrVal){
                        $updatedAttrs[$attrName] = $inputAttrVal;
                    }
                }

                if(!empty($updatedAttrs) && !$isError){

                    $apiUrl = $this->StringsApiServiceCatelog->getUrl(
                        'load-balancer',
                        "/LoadBalancer/update/$deviceId"
                    );
                    $body = json_encode($updatedAttrs);
                    if(!$this->QueueJob->addJob($apiUrl,$body)){
                        $isError = true;
                        $message = "Failed to schedule job to update attributes.";
                    }
                    break;

                    if(!$isError){
                        $this->Device->id = $deviceId;
                        $result = $this->Device->saveField('status','building',true);
                    }
                }
            }

            if(!$isError) {
                $redirectUri = "/Devices/view/$deviceId";
            }

            $this->outputAjaxFormResponse($message, $isError, $redirectUri);
        }
        else
            $this->render('configure_loadbalancer');
    }

    public function manageNodes($deviceId){

        $this->loadModel('QueueJob');

        $device = $this->Device->find('first',array(
            'contain' => array(
                'DeviceAttribute' => array(
                    'conditions' => array(
                        'DeviceAttribute.var' => 'implementation.nodes'
                    )
                )
            ),
            'conditions' => array(
                'Device.id' => $deviceId
            )
        ));

        if(empty($device)){
            $this->setFlash('Device does not exist.');
            $this->redirect(array('action' => 'index'));
        }

        $this->redirectIfNotActive($device);

        //Extract the nodes
        $nodes = array();
        if(!empty($device['DeviceAttribute'])) {
            $nodesAttr = $device['DeviceAttribute'][0];
            $nodesDetails = json_decode($nodesAttr['val'], true);
            $nodes = Hash::extract($nodes,'{n}.address');
        }

        if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";
            $redirectUri = null;

            //Validate input (best effort)
            $validInput = true;
            $newNodes = $this->request->data('nodes');
            if(empty($newNodes)) {
                $newNodes = array();
            }
            else {
                $ipPattern = '/([0-9]{1,3}\.){3,}[0-9]{1,3}/';
                foreach($newNodes as $node){
                    if(!preg_match($ipPattern,$node)){
                        $isError = true;
                        $message = "$node is not a valid ip address.";
                        break;
                    }
                }
            }

            if(!$isError){

                $changedNodes = false;

                //Diff newNodes to determine which nodes
                //need to be added and which need to be
                //removed
                $removeNodes = array_diff($nodes, $newNodes);
                $addNodes = array_diff($newNodes, $nodes);

                if(!empty($addNodes)){
                    $apiUrl = $this->StringsApiServiceCatelog->getUrl(
                        'load-balancer',
                        "/LoadBalancers/addNodes/$deviceId"
                    );
                    $body = json_encode($addNodes);
                    $result = $this->QueueJob->addJob($apiUrl, $body);
                    if(!$result){
                        $isError = true;
                        $message = 'Failed to schedule job to add new nodes.';
                    }
                    else
                        $changedNodes = true;
                }

                if(!$isError && !empty($removeNodes)){
                    $apiUrl = $this->StringsApiServiceCatelog->getUrl(
                        'load-balancer',
                        "/LoadBalancers/removeNodes/$deviceId"
                    );
                    $body = json_encode($removeNodes);
                    $result = $this->QueueJob->addJob($apiUrl, $body);
                    if(!$result){
                        $isError = true;
                        $message = 'Failed to schedule job to remove nodes.';
                    }
                    else
                        $changedNodes = true;
                }

                if($changedNodes){
                    $this->Device->id = $deviceId;
                    $this->Device->saveField('status','building',true);
                }

                if(!$isError) { 
                    $redirectUri = $this->referer();
                }
            }

            $this->outputAjaxFormResponse($message,$isError,$redirectUri);
        }

        if(!empty($nodes))
            $nodes = Hash::combine($nodes,'{n}','{n}');

        $this->set(array(
            'device' => $device,
            'nodes' => $nodes
        )); 
    }

    protected function redirectIfNotActive($device){
    
        if($device['Device']['status'] != 'active'){
            $this->setFlash('This device is currently undergoing another operation. It cannot be modified until the previous operation completes.');
            $this->redirect($this->referer(array('action' => 'index')));
        }
    }
}
