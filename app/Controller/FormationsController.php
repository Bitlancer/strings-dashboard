<?php

class FormationsController extends AppController
{

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
     * Home screen containing list of formation and create formation CTA
     */
    public function index() {

        $this->loadModel('Implementation');

		//Verify this organization has setup one or more infrastructure providers
        $isInfraProviderConfigured = $this->Implementation->
            hasOrganizationConfiguredServiceProvider(
                $this->Auth->user('organization_id'),'infrastructure');

        if(!$isInfraProviderConfigured){
            $this->setFlash('Please setup an infrastructure provider.');
        }

        $this->DataTables->setColumns(array(
            'Name' => array(
                'model' => 'Formation',
                'column' => 'name'
            )
        ));

        if($this->request->isAjax()){

            $this->DataTables->process(
                array(
                    'contain' => array(),
                    'fields' => array(
                        'Formation.*'
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

        $formation = $this->Formation->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Formation.id' => $id
            )
        ));

        if(empty($formation)){
            $this->setFlash('Formation does not exist.');
            $this->redirect(array('action' => 'index'));
        }

        $this->DataTables->setColumns(array(
            'Device' => array(
                'model' => 'Device',
                'column' => 'name'
            ),
            'Role' => array(
                'model' => 'Role',
                'column' => 'name'
            ),
        ),'devices');

        $this->set(array(
            'formation' => $formation,
            'isAdmin' => $this->Auth->User('is_admin')
        ));
    }

    public function delete($id=null){

        $this->loadModel('QueueJob');

        $formation = $this->Formation->find('first',array(
            'contain' => array(
                'Device',
            ),
            'conditions' => array(
                'Formation.id' => $id
            )
        ));

        if(empty($formation))
            throw new NotFoundException('Formation does not exist.');

        if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";
            $redirectUri = false;

            $formationName = $formation['Formation']['name'];

            if(!isset($this->request->data['confirm'])){
                $isError = true;
                $message = "Please confirm you would like to delete $formationName.";
            }
            elseif($this->request->data['confirm'] != $formationName){
                $isError = true;
                $message = "Incorrect formation name. Please enter <strong>$formationName</strong> to delete this formation.";
            }
            else {

                $devices = array();
                foreach($formation['Device'] as $device){
                    $devices[] = array(
                        'id' => $device['id'],
                        'status' => 'deleting'
                    );
                }

                $formationAndDevices = array(
                    'Formation' => array(
                        'id' => $id,
                        'status' => 'deleting'
                    ),
                    'Device' => $devices
                );

                if($this->Formation->saveAll($formationAndDevices)){
                    $this->QueueJob->addJob(STRINGS_API_URL . '/Formations/delete/' . $id);
                    $message = "Formation $formationName has been scheduled for deletion.";
                    $redirectUri = '/Formations';
                }
                else {
                    $isError = true;
                    $message = "Unable to delete formation.";
                }
            }

            if($isError){
                echo json_encode(array(
                    'isError' => $isError,
                    'message' => $message
                ));
            }
            else {
                $this->setFlash($message,'success');
                echo json_encode(array(
                    'redirectUri' => $redirectUri
                ));
            }
        }

        $this->set(array(
            'formation' => $formation
        ));
    }

    /**
     * Edit a formation
     */
    public function edit($id=null){

        $formation = $this->Formation->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Formation.id' => $id,
            )
        ));

        if(empty($formation)){
            $this->setFlash('This formation does not exist.');
            $this->redirect(array('action' => 'index'));
        }

        if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            $validFields = array('name');
            $this->Formation->id = $id;
            if($this->Formation->save($this->request->data,true,$validFields)){
                $message = 'Updated formation ' . $formation['Formation']['name'] . '.';
            }
            else {
                $isError = true;
                $message = $this->Formation->validationErrorsAsString();
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
                'formation' => $formation
            ));
        }
    }
    
    public function devices($id=null){

         $formation = $this->Formation->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Formation.id' => $id,
            )
        ));

        if(empty($formation))
            throw new NotFoundException('Formation does not exist');

        $this->DataTables->setColumns(array(
            'Device' => array(
                'model' => 'Device',
                'column' => 'name'
            ),
            'Role' => array(
                'model' => 'Role',
                'column' => 'name'
            ),
        ));

        if($this->request->isAjax()){

            $this->DataTables->process(
                array(
                    'contain' => array(
                        'DeviceType',
                        'Role'
                    ),
                    'fields' => array(
                        'Device.*','DeviceType.*','Role.*'
                    ),
                    'conditions' => array(
                        'Device.formation_id' => $id
                    )
                ),
                $this->Formation->Device
            ); 
 
            $this->set(array(
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'formation' => $formation
            ));
        }
    }
    

    public function deleteDevice($deviceId=null){

        $this->loadModel('QueueJob');

        $device = $this->Formation->Device->find('first',array(
            'contain' => array(
                'BlueprintPart'
            ),
            'conditions' => array(
                'Device.id' => $deviceId
            )
        ));

        if(empty($device))
            throw new NotFoundException('Device does not exist.');

        $deviceName = $device['Device']['name'];

        if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";
            $redirectUri = false;

            //Validate confirmation text
            if(!isset($this->request->data['confirm'])){
                $isError = true;
                $message = "Please confirm you would like to delete $deviceName";
            }
            elseif($this->request->data['confirm'] != $deviceName){
                $isError = true;
                $message = "Incorrect device name. Please enter <strong>$deviceName</strong> to delete this device.";
            }
            else {

                //Verify we can delete this device from the formation, 
                //check min requirement for its blueprint part id
                $formationId = $device['Device']['formation_id'];
                $blueprintPartId = $device['Device']['blueprint_part_id'];
                $blueprintPartMin = $device['BlueprintPart']['minimum'];
                $blueprintPartCount = $this->Formation->Device->find('count',array(
                    'conditions' => array(
                        'Device.formation_id' => $formationId,
                        'Device.blueprint_part_id' => $blueprintPartId,
                        'Device.status' => 'active'
                    )
                ));

                if(($blueprintPartCount - 1) < $blueprintPartMin){
                    $blueprintPartName = $device['BlueprintPart']['name'];
                    $isError = true;
                    $message = "You cannot delete this device. The Blueprint that this Formation was created from requires at least $blueprintPartMin $blueprintPartName(s).";
                }
                else {
                    $this->Formation->Device->id = $deviceId;
                    if($this->Formation->Device->saveField('status','deleting')){
                        $this->QueueJob->addJob(STRINGS_API_URL . '/Instances/delete/' . $deviceId);
                        $message = "Device $deviceName has been scheduled for deletion.";
                        $redirectUri = '/Devices';
                    }
                    else {
                        $isError = true;
                        $message = 'Unable to delete this device.';
                    }
                }
            }

            if($isError){
                echo json_encode(array(
                    'isError' => $isError,
                    'message' => $message
                ));
            }
            else {
                $this->setFlash($message,'success');
                echo json_encode(array(
                    'redirectUri' => $redirectUri
                ));
            }
        }

        $this->set('device',$device);
    }

    protected function redirectIfNotActive($formation){

        if($device['Formation']['status'] != 'active'){
            $this->setFlash('This formation is currently undergoing another operation. It cannot be modified until the previous operation completes.');
            $this->redirect($this->referer(array('action' => 'index')));
        }
    }

/**
 * Add a device to a formation
 */
    public function _setupWizardAddDevice(){

        $this->Wizard->steps = array(
            'deviceCounts',
            'configureDevices'
        );

        $this->Wizard->lockdown = true;                     //Prevent user from navigating between steps
        $this->Wizard->nestedViews = true;                  //Store view fields within action sub-folder
        $this->Wizard->completeUrl = '/Formations/';        //User is redirected here after wizard completion
        $this->Wizard->cancelUrl = '/Formations/';          //User is redirected here on wizard cancellation 
    }

    public function addDevice($step=null){

        //First call to this method includes the formation's
        //ID. Get the formation and add to the wizard's session
        if(preg_match('/^[0-9]+$/',$step)){

            $formationId = $step;

            $formation = $this->Formation->find('first',array(
                'contain' => array(),
                'conditions' => array(
                    'Formation.id' => $formationId
                )
            ));

            if(empty($formation))
                throw new NotFoundException('Formation does not exist.');

            $this->Wizard->save('_formation',$formation,true);
        }

        //Set title
        $formation = $this->Wizard->read('_formation');
        if(empty($formation))
            throw new InternalErrorException('Session does not contain formation details.');
        $this->set('title_for_layout', $formation['Formation']['name']);

        //Process wizard step
        $this->Wizard->process($step);
    }

    public function _prepareAddDeviceDeviceCounts(){

        $this->loadModel('Device');

        $formationId = $this->Wizard->read('_formation.Formation.id');

        //Get blueprint part counts for each blueprint part
        $existingBlueprintPartCounts = $this->Device->find('all',array(
            'contain' => array(),
            'fields' => array(
                'blueprint_part_id',
                'count(blueprint_part_id)',
            ),
            'conditions' => array(
                'Device.formation_id' => $formationId,
            ),
            'group' => 'Device.blueprint_part_id'
        ));

        $existingBlueprintPartCounts = Hash::combine(
            $existingBlueprintPartCounts,
            '{n}.Device.blueprint_part_id',
            '{n}.{n}.count(blueprint_part_id)');

        //Get list of blueprints
        $formation = $this->Formation->find('first',array(
            'contain' => array(
                'Blueprint' => array(
                    'BlueprintPart' => array(
                        'Role'
                    )
                )
            ),
            'conditions' => array(
                'Formation.id' => $formationId
            )
        ));

        if(empty($formation))
            throw new NotFoundException('Formation does not exist.');

        $blueprintParts = $formation['Blueprint']['BlueprintPart'];

        //Update blueprint min and max
        $updatedBlueprintParts = array();
        foreach($blueprintParts as $part){

            $partId = $part['id'];
            $partMin = $part['minimum'];
            $partMax = $part['maximum'];
            $currentDeviceCount = $existingBlueprintPartCounts[$partId];
            
            //Min
            $partMin = $currentDeviceCount >= $partMin ? 0 : $partMin;

            //Max
            $partMax = $currentDeviceCount >= $partMax ? 0 : $partMax - $currentDeviceCount;

            $part['maximum'] = $partMax;
            $part['minimum'] = $partMin;

            $updatedBlueprintParts[] = $part;
        }

        $this->Wizard->save('_deviceCounts.BlueprintParts',$updatedBlueprintParts);
        $this->set('blueprintParts',$updatedBlueprintParts);
    }

    public function _processAddDeviceDeviceCounts(){

        $blueprintParts = $this->Wizard->read('_deviceCounts.BlueprintParts');
        $blueprintParts = Hash::combine($blueprintParts,'{n}.id','{n}');

        $blueprintPartCounts = $this->request->data['blueprintPartCounts'];

        //Validate count for each part & ensure at least one device is being spun up
        $totalDeviceCount = 0;
        foreach($blueprintPartCounts as $partId => $count){

            $blueprintPart = $blueprintParts[$partId];
            $min = $blueprintPart['minimum'];
            $max = $blueprintPart['maximum'];
            $name = $blueprintPart['name'];

            if($count > $max || $count < $min){
                $this->setFlash("Invalid count supplied for $name.");
                return false;
            }

            $totalDeviceCount += $count;
        }
        if($totalDeviceCount == 0){
            $this->setFlash("You don't want to add a new device?");
            return false;
        }

        return true;
    }

    public function _prepareAddDeviceConfigureDevices(){

        $data = $this->Wizard->read('_configureDevices');
        if(empty($data)){
            
            $implementationId = $this->Wizard->read('_formation.Formation.implementation_id');
            $blueprintPartCounts = $this->Wizard->read('deviceCounts.blueprintPartCounts');
            $dictionaryId = $this->Wizard->read('_formation.Formation.dictionary_id');

            $data = $this->_prepareGenericConfigureDevices($implementationId,
                                                          $blueprintPartCounts,
                                                          $dictionaryId);

            $this->Wizard->save(null,$data,true);
        }

        $this->set($data);
    }

    public function _processAddDeviceConfigureDevices(){

        $this->loadModel('Device');
        $this->loadModel('HieraVariable');
        $this->loadModel('QueueJob');
        $this->loadModel('DictionaryWord');

        $devices = $this->Wizard->read('_configureDevices.devices');
        $partsModulesVariables = $this->Wizard->read('_configureDevices.blueprintPartModulesAndVariables');
        $dictionaryId = $this->Wizard->read('_formation.Formation.dictionary_id');
        $dictionaryWordIds = $this->Wizard->read('_configureDevices.dictionaryWordIds');
        $formationId = $this->Wizard->read('_formation.Formation.id');
        $implementationId = $this->Wizard->read('_formation.Formation.implementation_id');

        list($devices,$infraConfigErrors) = $this->_parseAndValidateDevices($devices,$implementationId);
        list($variables,$systemConfigErrors) = $this->_parseAndValidateDevicesVariables($devices,$partsModulesVariables);

        $devicesInErrorState = array_keys($infraConfigErrors) + 
                               array_keys($systemConfigErrors);

        $this->set(array(
            'devicesInErrorState' => $devicesInErrorState,
            'infraConfigErrors' => $infraConfigErrors,
            'systemConfigErrors' => $systemConfigErrors
        ));

        if(empty($devicesInErrorState)){

            //Add formation id to devices
            foreach($devices as $key => $device)
                $devices[$key]['Device']['formation_id'] = $formationId;

            //Validate data (best effort) before any saves begin
            if(!empty($variables) && !$this->HieraVariable->saveAll($variables,array('validate' => 'only'))){
                $this->setFlash('We encountered an error while saving the device variables.');
                $this->sLog('Error ecountered while validating the device variables. ' . 
                    $this->HieraVariable->validationErrorsAsString(true));
            }
            else {

                //Save the devices
                //Not sure why saveMany with deep = true doesn't work
                //Turn validation b/c the device attributes need a device_id to validate
                //but it will not get an id until the device is created
                if(!$this->Device->saveMany($devices,array('validate' => 'false','deep' => true))){
                    $this->setFlash('We encountered an error while saving the new devices.');
                    $this->sLog('Error encountered while saving the new devices. ' .
                        $this->Device->validationErrorsAsString(true));

                    return false;
                }

                //Mark dictionary words as used
                $this->DictionaryWord->markAsUsed($dictionaryWordIds);

                //Save the device variables
                if(!empty($variables) && !$this->HieraVariable->saveAll($variables)){
                    $this->setFlash('We encountered an error while saving the device variables.');
                    $this->sLog('Error encountered while saving the device variables. ' .
                        $this->HieraVariable->validationErrorsAsString(true));
                }

                //Add the Q jobs
                $newDeviceIds = $this->Device->getInsertIds();
                $qJobs = array();
                $templateQJob = array(
                    'body' => '',
                    'http_method' => 'post',
                    'timeout_secs' => 60,
                    'remaining_retries' => 20,
                    'retry_delay_secs' => 60 
                );
                foreach($newDeviceIds as $newDeviceId){
                    $templateQJob['url'] = STRINGS_API_URL . "/Instances/create/$newDeviceId";
                    $qJobs[] = $templateQJob;
                }

                if(!$this->QueueJob->saveAll($qJobs)){
                    $this->setFlash('We encountered an error while creating jobs to create your devices.');
                    $this->sLog('Error encountered while saving new device QueueJobs. ' .
                        $this->QueueJob->validationErrorsAsString(true));

                }

                return true;
            }
        } 

        return false;
    }

/**
 * Formation creation wizard
 */

    public function _setupWizardCreate(){

        $this->Wizard->steps = array(
            'formationSettings',
            'selectBlueprint',
            'deviceCounts',
            'configureDevices'
        );

        $this->Wizard->lockdown = true;                     //Prevent user from navigating between steps
        $this->Wizard->nestedViews = true;                  //Store view fields within action sub-folder
        $this->Wizard->completeUrl = '/Formations/';        //User is redirected here after wizard completion
        $this->Wizard->cancelUrl = '/Formations/';          //User is redirected here on wizard cancellation
    }

    public function create($step=null){

        $this->set('title_for_layout', 'Create Formation');
        $this->Wizard->process($step);
    }
    
    public function _prepareCreateFormationSettings(){

        $this->loadModel('Implementation');
        $this->loadModel('Dictionary');

        //Get a list of infrastructure providers
        $implementations = $this->Implementation->Provider->find('all',array(
            'link' => array(
                'Implementation',
                'Service'
            ),
            'fields' => array(
                'Implementation.*'
            ),
            'conditions' => array(
                'Implementation.organization_id' => $this->Auth->User('organization_id'),
                'Service.name' => 'infrastructure'
            )
        ));
        $implementations = Hash::combine($implementations,'{n}.Implementation.id','{n}.Implementation.name');

        //Get a list of dictionaries
        $dictionaries = $this->Dictionary->find('all',array('contain' => array()));
        $dictionaries = Hash::combine($dictionaries,'{n}.Dictionary.id','{n}.Dictionary.name');

        $this->set(array(
            'implementations' => $implementations,
            'dictionaries' => $dictionaries,
        ));
    }
    
    public function _processCreateFormationSettings(){

        $this->loadModel('Implementation');
        $this->loadModel('Dictionary');

        //Validate name
        if(!isset($this->request->data['Formation']['name']) || empty($this->request->data['Formation']['name'])){
            $this->setFlash('A formation name is required','error');
            return false;
        }
        $this->Formation->set(array(
            'Formation' => array(
                'name' => $this->request->data['Formation']['name']
            )
        ));
        $result = $this->Formation->validates(array(
            'fieldList' => array(
                'name'
            )
        ));
        if(!$result){
            $this->setFlash('Invalid formation name. ' . $this->Formation->validationErrorsAsString(),'error');
            return false;
        }

        //Validate implementation
        if(!isset($this->request->data['Implementation']['id']) || empty($this->request->data['Implementation']['id'])){
            $this->setFlash('An infrastructure provider required','error');
            return false;
        }
        $implementationId = $this->request->data['Implementation']['id'];
        $implementation = $this->Implementation->Provider->find('first',array(
            'link' => array(
                'Implementation',
                'Service'
            ),
            'conditions' => array(
                'Implementation.id' => $implementationId,
                'Service.name' => 'infrastructure'
            )
        ));
        if(empty($implementation)){
            $this->setFlash('Invalid provider supplied','error');
            return false;
        }

        //Validate dictionary
        if(!isset($this->request->data['Dictionary']['id']) || empty($this->request->data['Dictionary']['id'])){
            $this->Session->setFlash(__('Infrastructure provider required'),'default',array(),'error');
            return false;
        }
        $dictionaryId = $this->request->data['Dictionary']['id'];
        if(!$this->Dictionary->exists($dictionaryId)){
            $this->Session->setFlash(__('Invalid dictionary supplied'),'default',array(),'error');
            return false;
        }

        return true;
    }

    public function _prepareCreateSelectBlueprint(){

        $this->loadModel('Blueprint');

        $this->DataTables->setColumns(array(
            'Blueprint' => array(
                'model' => 'Blueprint',
                'column' => 'name'
            ),
        ));

        if($this->request->is('ajax')){

            $this->DataTables->process(
                array(
                    'fields' => array(
                        'Blueprint.id','Blueprint.name','Blueprint.short_description'
                    ),
                ),
                $this->Blueprint
            );

            $this->set(array(
                'isAdmin' => $this->Auth->User('is_admin')
            ));

        }
    }

    public function _processCreateSelectBlueprint(){

        $this->loadModel('Blueprint');

        $blueprintId = $this->request->data['Blueprint']['id'];

        if($this->Blueprint->exists($blueprintId))
            return true;

        return false;
    }

    public function _prepareCreateDeviceCounts(){

        $this->loadModel('BlueprintPart');

        $blueprintId = $this->Wizard->read('selectBlueprint.Blueprint.id');

        $blueprintParts = $this->BlueprintPart->find('all',array(
            'contain' => array(),
            'conditions' => array(
                'BlueprintPart.blueprint_id' => $blueprintId
            )
        ));

        $blueprintParts = Hash::extract($blueprintParts,'{n}.BlueprintPart');

        $this->Wizard->save('_deviceCounts.blueprintParts',$blueprintParts);
        $this->set('blueprintParts',$blueprintParts);
    }

    public function _processCreateDeviceCounts(){

        $blueprintParts = $this->Wizard->read('_deviceCounts.blueprintParts');
        $blueprintParts = Hash::combine($blueprintParts,'{n}.id','{n}');

        //Validate count for each blueprint part and build count data structure
        $countsValid = true;
        $partCounts = $this->request->data['blueprintPartCounts'];
        foreach($partCounts as $id => $count){
            $blueprintPart = $blueprintParts[$id];
            $expectedMin = $blueprintPart['minimum'];
            $expectedMax = $blueprintPart['maximum'];
            if($count < $expectedMin || $count > $expectedMax){
                $countsValid = false;
                $this->Session->setFlash(__('Invalid device count specified for ' . $blueprintPart['name']),'default',array(),'error');
                break;
            }
        }

        return $countsValid;
    }

    public function _prepareCreateConfigureDevices(){

        $this->loadModel('Implementation');
        $this->loadModel('DictionaryWord');
        $this->loadModel('BlueprintPart');

        $data = $this->Wizard->read('_configureDevices');
        if(empty($data)){

            //Get session data needed for processing during this step;
            $implementationId = $this->Wizard->read('formationSettings.Implementation.id');
            $blueprintPartCounts = $this->Wizard->read('deviceCounts.blueprintPartCounts');
            $dictionaryId = $this->Wizard->read('formationSettings.Dictionary.id');

            $data = $this->_prepareGenericConfigureDevices($implementationId,
                                                          $blueprintPartCounts,
                                                          $dictionaryId);

            $this->Wizard->save(null,$data,true);
        }

        $this->set($data);
    }

    public function _processCreateConfigureDevices(){

        $this->loadModel('DictionaryWord');
        $this->loadModel('QueueJob');
        $this->loadModel('HieraVariable');

        $devices = $this->Wizard->read('_configureDevices.devices');
        $partsModulesVariables = $this->Wizard->read('_configureDevices.blueprintPartModulesAndVariables');
        $dictionaryId = $this->Wizard->read('formationSettings.Dictionary.id');
        $dictionaryWordIds = $this->Wizard->read('_configureDevices.dictionaryWordIds');
        $implementationId = $this->Wizard->read('formationSettings.Implementation.id');
        $blueprintId = $this->Wizard->read('selectBlueprint.Blueprint.id');
        $formationName = $this->Wizard->read('formationSettings.Formation.name');

        list($devices,$infraConfigErrors) = $this->_parseAndValidateDevices($devices,$implementationId);
        list($variables,$systemConfigErrors) = $this->_parseAndValidateDevicesVariables($devices,$partsModulesVariables);

        $devicesInErrorState = array_keys($infraConfigErrors) + 
                               array_keys($systemConfigErrors);

        $this->set(array(
            'devicesInErrorState' => $devicesInErrorState,
            'infraConfigErrors' => $infraConfigErrors,
            'systemConfigErrors' => $systemConfigErrors
        ));

        if(empty($devicesInErrorState)){

            $formation = array(
                'Formation' => array(
                    'implementation_id' => $implementationId,
                    'blueprint_id' => $blueprintId,
                    'dictionary_id' => $dictionaryId,
                    'name' => $formationName
                ),
                'Device' => $devices
            );

            $qJob = array(
                'QueueJob' => array(
                    'url' => STRINGS_API_URL . '/Formations/create/',
                    'body' => '',
                    'http_method' => 'post',
                    'timeout_secs' => 60,
                    'remaining_retries' => 10,
                    'retry_delay_secs' => 60
                )
            ); 

            //Do our best to verify all of the data is valid
            //before we start saving
            $this->Formation->set($formation);
            $this->QueueJob->set($qJob);
            if(!$this->Formation->validates()){ //This only validates the formation
                $this->setFlash('We encountered an error while saving this formation.');
                $this->sLog("Error encountered while validating formation. " .
                    $this->Formation->validationErrorsAsString(true));
            }
            elseif(!$this->QueueJob->validates()){
                $this->setFlash('We encountered an error while scheduling this formation to be created.');
                $this->sLog("Error encountered while validating QueueJob. "  .
                    $this->QueueJob->validationErrorsAsString());
            }
            elseif(!empty($variables) && !$this->HieraVariable->saveAll($variables,array('validate' => 'only'))){
                $this->setFlash('We encountered an error while saving your device variables.');
                $this->sLog("Error encountered while validating hiera variables. " .
                    $this->HieraVariable->validationErrorsAsString(true));
            }
            else {
             
                //Must turn off validation b/c device validation will fail until
                //they have not been assigned valid formation_ids, which won't
                //occur until the formation has been saved.
                $this->Formation->create();
                $result = $this->Formation->saveAll($formation,array(
                    'validate' => false,
                    'deep' => true,
                ));
                if(!$result){
                    $this->setFlash('We encountered an error while " .
                        "creating your formation.');
                    $this->log("Error encountered while saving formation. " .
                        $this->Formation->validationErrorsAsString(true));
                    return false;
                }

                //Mark dictionary words as used
                $this->DictionaryWord->markAsUsed($dictionaryWordIds);

                //Add create formation Q job
                $qJob['QueueJob']['url'] .= $this->Formation->id;
                $this->QueueJob->create();
                if(!$this->QueueJob->save($qJob)){
                    $this->setFlash('We encountered an error while creating a job to build this formation.');
                    $this->log("Error encountered while saving QueueJob." . 
                        $this->QueueJob->validationErrorsAsString());
                }

                //Create variables
                if(!empty($variables) && !$this->HieraVariable->saveMany($variables)){
                    $msg = "Your formation has been scheduled for creation however we " .
                        "encountered an error while setting one or more device " .
                        "configuration variables. ";
                    $this->setFlash($msg);

                    $this->log("Error encountered while saving hiera variables. " .
                        $this->HieraVariable->validationErrorsAsString(true));
                }

                return true;
            }
        }

        return false;
    }

    private function _prepareGenericConfigureDevices($implementationId,$blueprintPartCounts,$dictionaryId){

        $this->loadModel('Role');

        //Get provider regions and flavors
        list($regions,$flavors) = $this->_getProviderRegionsAndFlavors($implementationId);

        //Get list of blueprint parts and associated data
        $blueprintPartIds = array_keys($blueprintPartCounts);
        $blueprintParts = $this->_getBlueprintPartData($blueprintPartIds);

        //Get variables per blueprint part
        $blueprintPartModulesAndVariables = array();
        foreach($blueprintParts as $blueprintPart){
            $roleId = $blueprintPart['BlueprintPart']['role_id'];
            $blueprintPartId = $blueprintPart['BlueprintPart']['id'];
            $blueprintPartModulesAndVariables[$blueprintPartId] =
                $this->Role->getRoleVariables($roleId);
        }

        //Reserve dictionary words
        $deviceCount = 0;
        foreach($blueprintPartCounts as $id => $count)
            $deviceCount += $count;
        $dictionaryWords = $this->_reserveXWords($dictionaryId,$deviceCount);
        $dictionaryWordIds = Hash::extract($dictionaryWords,'{n}.DictionaryWord.id');

        //Create devices psuedo structure
        $devices = $this->_createPsuedoDevicesStructure($blueprintPartCounts,
                                            $blueprintParts,
                                            $dictionaryWords);
        $data = array(
            'regions' => $regions,
            'flavors' => $flavors,
            'blueprintPartModulesAndVariables' => $blueprintPartModulesAndVariables,
            'devices' => $devices,
            'dictionaryWordIds' => $dictionaryWordIds
        );

        return $data;
    } 

    private function _getProviderRegionsAndFlavors($implementationId){

        $this->loadModel('Implementation');
        
        //Get list of provider regions
        $regions = $this->Implementation->getRegions($implementationId);
        if(empty($regions))
            throw new InternalErrorException('Regions have not been defined for this provider');
        $regions = Hash::combine($regions,'{n}.id','{n}.name');
        
        //Get a list of provider flavors
        $flavors = $this->Implementation->getFlavors($implementationId);
        if(empty($flavors))
            throw new InternalErrorException('Flavors have not been defined for this provider');
        $flavors = Hash::combine($flavors,'{n}.id','{n}.description');
        
        return array($regions,$flavors);
    }

    private function _getBlueprintPartData($blueprintPartIds){

        $this->loadModel('BlueprintPart');
        
        if(!is_array($blueprintPartIds))
            $blueprintPartIds = array($blueprintPartIds);
            
        $blueprintParts = $this->BlueprintPart->find('all',array(
            'contain' => array(
                'DeviceType',
                'Role',
            ),
            'conditions' => array(
                'BlueprintPart.id' => $blueprintPartIds
            )
        ));

        $blueprintParts = Hash::combine($blueprintParts,'{n}.BlueprintPart.id','{n}');
        
        return $blueprintParts;
    }

    private function _reserveXWords($dictionaryId,$count){

        $this->loadModel('DictionaryWord');

        $dictionaryWords = $this->DictionaryWord->reserve($dictionaryId,$count);
        if($dictionaryWords === false)
            throw new InternalErrorException('Dictionary does not contain enough free words to accomodate this formation');
        $dictionaryWords = Hash::combine($dictionaryWords,'{n}.DictionaryWord.id','{n}');
        
        return $dictionaryWords;
    }

    private function _createPsuedoDevicesStructure($blueprintPartCounts,$blueprintParts,$dictionaryWords){

        $devices = array();
        $nextDevicePsuedoId = 0;
        foreach($blueprintPartCounts as $blueprintPartId => $deviceCount){
            for($x=0;$x<$deviceCount;$x++){
                
                $dictionaryWord = array_shift($dictionaryWords);
                $name = $dictionaryWord['DictionaryWord']['word'];
                
                $blueprintPart = $blueprintParts[$blueprintPartId];
                
                $devices[] = array(
                    'psuedoId' => $nextDevicePsuedoId++,
                    'deviceTypeId' => $blueprintPart['DeviceType']['id'],
                    'blueprintPartId' => $blueprintPart['BlueprintPart']['id'],
                    'roleId' => $blueprintPart['Role']['id'],
                    'name' => $name,
                    'blueprintPartName' => $blueprintPart['BlueprintPart']['name']
                );
                
            }
        }
        
        return $devices;
    }

    private function _parseAndValidateDevices($devices,$implementationId){

        $this->loadModel('Implementation');
        $this->loadModel('Config');
   
        $models = array();
        $errors = array();
    
        $defaultImageId = $this->Implementation->getDefaultImageId($implementationId);
        list($intDnsSuffix,$extDnsSuffix) = $this->Config->getDnsSuffixes();
        
        list($regions,$flavors) = $this->_getProviderRegionsAndFlavors($implementationId);
        $regionIds = array_keys($regions);
        $flavorIds = array_keys($flavors);
        
        foreach($devices as $device){
            
            $deviceErrors = array();
            
            $psuedoId = $device['psuedoId'];
            $name = $device['name'];
            $blueprintPartId = $device['blueprintPartId'];
            $roleId = $device['roleId'];
            $deviceTypeId = $device['deviceTypeId'];
            
            $deviceModel = array(
                'Device' => array(
                    'implementation_id' => $implementationId,
                    'blueprint_part_id' => $blueprintPartId,
                    'role_id' => $roleId,
                    'device_type_id' => $deviceTypeId,
                    'name' => $name
                ),
                'DeviceAttribute' => array()
            );
            
            
            //Verify input exists for this device
            if(!isset($this->request->data['Device'][$psuedoId])){
                $deviceErrors[] = 'Input missing.';
            }
            else {

                $deviceInput = $this->request->data['Device'][$psuedoId];

                //Region & DNS
                if(!isset($deviceInput['region']) || empty($deviceInput['region']))
                    $deviceErrors[] = 'Region is required.';
                elseif(!in_array($deviceInput['region'],$regionIds)){
                    $deviceErrors[] = 'Invalid region.';
                }
                else {
                    $regionId = $deviceInput['region'];
                                
                    $deviceModel['DeviceAttribute'][] = array(
                        'var' => 'implementation.region_id',
                        'val' => $regionId
                    );
                    
                    $regionName = $regions[$regionId];
                    
                    $deviceModel['DeviceAttribute'][] = array(
                        'var' => 'dns.internal.fqdn',
                        'val' => strtolower("$name.$regionName.$intDnsSuffix")
                    );
                    
                    $deviceModel['DeviceAttribute'][] = array(
                        'var' => 'dns.external.fqdn',
                        'val' => strtolower("$name.$regionName.$extDnsSuffix")
                    );
                }

                //Only applies to instances
                if($deviceTypeId == 1){
                
                    //Flavor
                    if(!isset($deviceInput['flavor']) || empty($deviceInput['flavor']))
                        $deviceErrors[] = 'Flavor is required.';
                    elseif(!in_array($deviceInput['flavor'],$flavorIds))
                        $deviceErrors[] = 'Invalid flavor.';
                    else {                  
                        $deviceModel['DeviceAttribute'][] = array(
                            'var' => 'implementation.flavor_id',
                            'val' => $deviceInput['flavor']
                        );
                    }

                    //Image id
                    $deviceModel['DeviceAttribute'][] = array(
                        'var' => 'implementation.image_id',
                        'val' => $defaultImageId
                    );

                }
            }
            
            if(!empty($deviceErrors))
                $errors[$psuedoId] = $deviceErrors;
            
            $models[$psuedoId] = $deviceModel;
        }
        
        return array($models,$errors);
    } 

    private function _parseAndValidateDevicesVariables($devices,$partsModulesVariables){

        $this->loadModel('HieraVariable');

        $variables = array();
        $errors = array();

        foreach($devices as $psuedoId => $device){

            $deviceErrors = array();

            //Variables only apply to instances (device_type_id = 1)
            if($device['Device']['device_type_id'] != 1)
                continue;

            $partId = $device['Device']['blueprint_part_id'];

            //This blueprint part does not have any variables
            if(!isset($partsModulesVariables[$partId]) || empty($partsModulesVariables[$partId]))
                continue;

            //Get external FQDN
            $deviceFqdn = "";
            $deviceAttrs = $device['DeviceAttribute'];
            foreach($deviceAttrs as $attr){
                if($attr['var'] == 'dns.external.fqdn')
                    $deviceFqdn = $attr['val'];
            }
            if(empty($deviceFqdn))
                throw new InternalErrorException('Could not determine devices FQDN.');

            $hieraKey = "fqdn/$deviceFqdn";

            $partVariables = $partsModulesVariables[$partId];
    
            $input = $this->request->data['Device'][$psuedoId]['variables'];

            list($deviceVariables,$deviceErrors) = 
                $this->HieraVariable->parseAndValidateDeviceVariables($partVariables,$input,$hieraKey);

            $variables = array_merge($variables,$deviceVariables);
        
            if(!empty($deviceErrors))
                $errors[$psuedoId] = $deviceErrors;
        }
    
        return array($variables,$errors);
    }
}
