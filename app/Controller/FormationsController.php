<?php

class FormationsController extends AppController
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
     * Home screen containing list of formation and create formation CTA
     */
    public function index() {

        $this->loadModel('Implementation');

		//Verify this organization has setup one or more infrastructure providers
        $hasInfraProvider = $this->Implementation->hasServiceProvider(
            $this->Auth->user('organization_id'),
            'infrastructure'
        );
        if(!$hasInfraProvider){
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
                'createCTADisabled' => !$hasInfraProvider || !$this->Auth->User('is_admin'),
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

                $this->Formation->id = $id;
                if($this->Formation->saveField('status','deleting',true)){

                    $deviceIds = Hash::extract($formation['Device'],'{n}.id');
                    $this->Formation->Device->updateAll(
                        array('Device.status' => "'deleting'"),
                        array('Device.id' => $deviceIds)
                    );

                    $apiUrl = $this->StringsApiServiceCatelog->getUrl(
                        'infrastructure',
                        "/Formations/delete/$id"
                    );

                    $this->QueueJob->addJob($apiUrl);
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
                        $apiUrl = $this->StringsApiServiceCatelog->getUrl(
                            'infrastructure',
                            "/Instances/delete/$deviceId"
                        );
                        $this->QueueJob->addJob($apiUrl);
                        $message = "$deviceName has been scheduled for deletion.";
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

        //Get list of blueprint parts
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
        $instanceVarDefs = $this->Wizard->read('_configureDevices.instanceVarDefs');
        $dictionaryId = $this->Wizard->read('_formation.Formation.dictionary_id');
        $dictionaryWordIds = $this->Wizard->read('_configureDevices.dictionaryWordIds');
        $formationId = $this->Wizard->read('_formation.Formation.id');
        $implementationId = $this->Wizard->read('_formation.Formation.implementation_id');

        list($errors, $devices) =
            $this->_parseAndValidateDevices($devices);

        $devicesInErrorState = array_keys($errors);

        $this->set(array(
            'devicesInErrorState' => $devicesInErrorState,
            'errors' => $errors
        ));

        if(!empty($devicesInErrorState))
            return false;

        //Add formation id to devices
        foreach($devices as $key => $device)
            $devices[$key]['Device']['formation_id'] = $formationId;

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

        //Add the Q jobs
        $newDeviceIds = $this->Device->getInsertIds();
        $apiUrl = $this->StringsApiServiceCatelog->getUrl('infrastructure');
        $qJobs = array();
        $templateQJob = array(
            'body' => '',
            'http_method' => 'post',
            'timeout_secs' => 60,
            'remaining_retries' => 20,
            'retry_delay_secs' => 60 
        );
        foreach($newDeviceIds as $newDeviceId){
            $templateQJob['url'] = $apiUrl . "/Instances/create/$newDeviceId";
            $qJobs[] = $templateQJob;
        }

        if(!$this->QueueJob->saveAll($qJobs)){
            $this->setFlash('We encountered an error while creating jobs to create your devices.');
            $this->sLog('Error encountered while saving new device QueueJobs. ' .
                $this->QueueJob->validationErrorsAsString(true));

        }

        return true;
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
        $instancesVarDefs = $this->Wizard->read('_configureDevices.instanceVarDefs');
        $dictionaryId = $this->Wizard->read('formationSettings.Dictionary.id');
        $dictionaryWordIds = $this->Wizard->read('_configureDevices.dictionaryWordIds');
        $implementationId = $this->Wizard->read('formationSettings.Implementation.id');
        $blueprintId = $this->Wizard->read('selectBlueprint.Blueprint.id');
        $formationName = $this->Wizard->read('formationSettings.Formation.name');

        list($errors, $devices) = $this->_parseAndValidateDevices($devices,
                                                                $implementationId,
                                                                $instancesVarDefs);

        $devicesInErrorState = array_keys($errors);

        $this->set(array(
            'devicesInErrorState' => $devicesInErrorState,
            'errors' => $errors
        ));

        if(!empty($devicesInErrorState))
            return false;

        $formation = array(
            'Formation' => array(
                'implementation_id' => $implementationId,
                'blueprint_id' => $blueprintId,
                'dictionary_id' => $dictionaryId,
                'name' => $formationName
            ),
            'Device' => $devices
        );

        //Do our best to verify all of the data is valid
        //before we start saving
        $this->Formation->set($formation);
        if(!$this->Formation->validates()){ //This only validates the formation
            $this->setFlash('We encountered an error while saving this formation.');
            $this->sLog("Error encountered while validating formation. " .
                $this->Formation->validationErrorsAsString(true));
        }
        else {
         
            //Must turn off validation b/c device validation will fail until
            //they have not been assigned valid formation_ids, which won't
            //occur until the formation has been saved.
            $this->Formation->create();
            $result = $this->Formation->saveAll($formation, array(
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
            $apiUrl = $this->StringsApiServiceCatelog->getUrl(
                'infrastructure',
                "/Formations/create/" . $this->Formation->id
            );
            $result = $this->QueueJob->addJob($apiUrl,"","post",60,10,10);
            if(!$result){
                $this->setFlash('We encountered an error while creating a job to build this formation.');
                $this->log("Error encountered while saving QueueJob." . 
                    $this->QueueJob->validationErrorsAsString());
            }

            return true;
        }

        return false;
    }

    private function _prepareGenericConfigureDevices($implementationId,$blueprintPartCounts,$dictionaryId){

        $this->loadModel('Role');

        //Get list of blueprint parts and associated data
        $blueprintPartIds = array_keys($blueprintPartCounts);
        $blueprintParts = $this->_getBlueprintPartData($blueprintPartIds);

        //Reserve dictionary words
        $deviceCount = 0;
        foreach($blueprintPartCounts as $id => $count)
            $deviceCount += $count;
        $dictionaryWords = $this->_reserveXWords($dictionaryId,$deviceCount);
        $dictionaryWordIds = Hash::extract($dictionaryWords,'{n}.DictionaryWord.id');

        //Create devices psuedo structure
        $devices = $this->_createPsuedoDevicesStructure($implementationId,
                                                        $blueprintPartCounts,
                                                        $blueprintParts,
                                                        $dictionaryWords);

        //Get form data foreach individual device
        foreach($devices as $key => $device){
            $formData = $this->FormationsAndDevices->getDeviceFormData($device);
            $device['formData'] = $formData;
            $devices[$key] = $device;
        }

        return array(
            'dictonaryWordIds' => $dictionaryWordIds,
            'devices' => $devices,
        );
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

    private function _createPsuedoDevicesStructure($implementationId,$blueprintPartCounts,$blueprintParts,$dictionaryWords){

        $devices = array();
        $nextDevicePsuedoId = 0;
        foreach($blueprintPartCounts as $blueprintPartId => $deviceCount){
            for($x=0;$x<$deviceCount;$x++){
                
                $dictionaryWord = array_shift($dictionaryWords);
                $name = $dictionaryWord['DictionaryWord']['word'];
                
                $blueprintPart = $blueprintParts[$blueprintPartId];
                
                $devices[] = array(
                    'Device' => array(
                        'psuedo' => true,
                        'id' => $nextDevicePsuedoId++,
                        'organization_id' => $this->Auth->User('organization_id'), 
                        'device_type_id' => $blueprintPart['DeviceType']['id'],
                        'implementation_id' => $implementationId,
                        'blueprint_part_id' => $blueprintPartId,
                        'role_id' => $blueprintPart['Role']['id'],
                        'name' => $name,
                    ),
                    'DeviceType' => array(
                        'name' => $blueprintPart['DeviceType']['name']
                    ),
                    'BlueprintPart' => array(
                        'name' => $blueprintPart['BlueprintPart']['name']
                    )
                );
            }
        }
        
        return $devices;
    }

    private function _parseAndValidateDevices($devices) {

        $errors = array();
        $models = array();
    
        foreach($devices as $device){

            $deviceErrors = array();
            $deviceModels = array();

            $psuedoId = $device['Device']['id'];
            
            //Verify input exists for this device
            if(!isset($this->request->data['Device'][$psuedoId])){
                $deviceErrors['general'][] = "Input missing.";
            }
            else {
                $deviceInput = $this->request->data['Device'][$psuedoId];
                list($deviceErrors,$deviceModels) = $this->FormationsAndDevices->parseAndValidateDevice($device, $deviceInput);
            }

            //Some devices, load-balancers for example, can return more
            //than one device
            if(isset($deviceModels['Device']))
                $models[] = $deviceModels;
            else
                $models = array_merge($models, $deviceModels);

            if(!empty($deviceErrors))
                $errors[$psuedoId] = $deviceErrors;
        }

        return array($errors, $models);
    }
}
