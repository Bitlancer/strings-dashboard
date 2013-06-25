<?php

class FormationsController extends AppController
{
    public function beforeFilter(){

        /*
         * Formation creation wizard settings
         */
        $this->Wizard->steps = array(
            'formationSettings',
            'selectBlueprint',
            'deviceCounts',
            'configureDevices'
        );
        $this->Wizard->lockdown = true;                     //Prevent user from navigating between steps
        $this->Wizard->nestedViews = true;                  //Store view fields within wizard/
        $this->Wizard->completeUrl = '/Formations/';        //User is redirected here after wizard completion
        $this->Wizard->cancelUrl = '/Formations/';          //User is redirected here on wizard cancellation
    }

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

		//Verify this organization has setup one or more infrastructure providers
        $this->loadModel('Implementation');
        $isInfraProviderConfigured = $this->Implementation->hasOrganizationConfiguredServiceProvider($this->Auth->user('organization_id'),'infrastructure');
        if(!$isInfraProviderConfigured){
            $this->setFlash('Please setup an infrastructure provider.');
        }

        $formationTableColumns = array(
            'Name' => array(
                'model' => 'Formation',
                'column' => 'name'
            )
        );

        if($this->request->isAjax()){

            //Datatables
            $findParameters = array(
                'fields' => array(
                    'Formation.id','Formation.name','Formation.status'
                )
            );

            $dataTable = $this->DataTables->getDataTable($formationTableColumns,$findParameters);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'formationTableColumns' => array_keys($formationTableColumns),
                'createCTADisabled' => !$isInfraProviderConfigured || !$this->Auth->User('is_admin'),
            ));
        }
    }

    public function view($id=null){

        $formation = $this->Formation->find('first',array(
            'contain' => array(
                'Device' => array(
                    'Role'
                )
            ),
            'conditions' => array(
                'Formation.id' => $id
            )
        ));

        if(empty($formation)){
            $this->setFlash('Formation does not exist.');
            $this->redirect(array('action' => 'index'));
        }

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

        if(empty($formation)){
            $this->setFlash('Formation does not exist.');
            $this->redirect(array('action' => 'index'));
        }

        if($this->request->is('post')){

            $formationName = $formation['Formation']['name'];

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
                $this->setFlash("Formation $formationName has been marked for deletion",'success');
            }
            else {
                $this->setFlash("Unable to delete formation.");
            }

            $this->redirect($this->referer(array('action' => 'index')));
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

    public function editDevices($id=null){

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

        $devicesTableColumns = array(
            'Device' => array(
                'model' => 'Device',
                'column' => 'name'
            ),
            'Role' => array(
                'model' => 'Role',
                'column' => 'name'
            ),
        );

        if($this->request->isAjax()){

            //Datatables
            $findParameters = array(
                'contain' => array(
                    'Role'
                ),
                'fields' => array(
                    'Device.id','Device.name','Device.status','Device.formation_id','Role.name'
                )
            );

            $dataTable = $this->DataTables->getDataTable($devicesTableColumns,$findParameters,$this->Formation->Device);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'devicesTableColumns' => array_keys($devicesTableColumns),
                'formation' => $formation
            ));
        }
    }

    protected function redirectIfNotActive($formation){

        if($device['Formation']['status'] != 'active'){
            $this->setFlash('This formation is currently undergoing another operation. It cannot be modified until the previous operation completes.');
            $this->redirect($this->referer(array('action' => 'index')));
        }
    }

/**
 * Formation creation wizard
 */
    public function wizard($step=null){

        $this->set('title_for_layout', 'Create Formation');
        $this->Wizard->process($step);
    }

    public function _prepareSelectBlueprint(){

        $blueprintTableColumns = array(
            'Blueprint' => array(
                'model' => 'Blueprint',
                'column' => 'name'
            ),
        );

        if($this->request->is('ajax')){

            $this->loadModel('Blueprint');

            //Datatables
            $findParameters = array(
                'fields' => array(
                    'Blueprint.id','Blueprint.name','Blueprint.short_description'
                ),
            );

            $dataTable = $this->DataTables->getDataTable($blueprintTableColumns,$findParameters,$this->Blueprint);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));

        }
        else {
            $this->set(array(
                'blueprintTableColumns' => array_keys($blueprintTableColumns),
            ));
        }
    }

    public function _processSelectBlueprint(){

        $this->loadModel('Blueprint');

        $blueprintId = $this->request->data['Blueprint']['id'];

        if($this->Blueprint->exists($blueprintId))
            return true;

        return false;
    }

    public function _prepareDeviceCounts(){

        $this->loadModel('BlueprintPart');

        $blueprintId = $this->Wizard->read('selectBlueprint.Blueprint.id');

        $blueprintParts = $this->BlueprintPart->find('all',array(
            'contain' => array(
                'Role'
            ),
            'conditions' => array(
                'BlueprintPart.blueprint_id' => $blueprintId
            )
        ));

        $this->set(array(
            'blueprintParts' => $blueprintParts
        ));
    }

    public function _processDeviceCounts(){

        $this->loadModel('BlueprintPart');

        //Get blueprint parts
        $blueprintId = $this->Wizard->read('selectBlueprint.Blueprint.id');
        $blueprintParts = $this->BlueprintPart->find('all',array(
            'contain' => array(),
            'conditions' => array(
                'BlueprintPart.blueprint_id' => $blueprintId
            )
        ));

        //Re-index array by id
        $blueprintParts = Hash::combine($blueprintParts,'{n}.BlueprintPart.id','{n}');

        //Validate count for each blueprint part and build count data structure
        $countsValid = true;
        $partCounts = $this->request->data['blueprintPartCounts'];
        foreach($partCounts as $id => $count){
            $blueprintPart = $blueprintParts[$id]['BlueprintPart'];
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

    public function _prepareFormationSettings(){

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

    public function _processFormationSettings(){

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
        if(!$this->Formation->validates()){
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

    public function _prepareConfigureDevices(){

        $this->loadModel('Implementation');
        $this->loadModel('DictionaryWord');
        $this->loadModel('BlueprintPart');

        $data = $this->Wizard->read('_configureDevices');
        if(empty($data)){

            $implementationId = $this->Wizard->read('formationSettings.Implementation.id');

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

            //Get list of blueprint parts and associated data
            $blueprintPartCounts = $this->Wizard->read('deviceCounts.blueprintPartCounts');
            $blueprintPartIds = array_keys($blueprintPartCounts);
            $blueprintParts = $this->BlueprintPart->find('all',array(
                'contain' => array(
                    'Role','DeviceType'
                ),
                'fields' => array(
                    'Role.*','DeviceType.*','BlueprintPart.*'
                ),
                'conditions' => array(
                    'BlueprintPart.id' => $blueprintPartIds
                )
            ));
            $blueprintParts = Hash::combine($blueprintParts,'{n}.BlueprintPart.id','{n}');
           
            //Get total device count
            $deviceCount = 0;
            foreach($blueprintPartCounts as $id => $count)
                $deviceCount += $count;

            //Select device names from dictionary and mark as reserved
            $dictionaryId = $this->Wizard->read('formationSettings.Dictionary.id');
            $dictionaryWords = $this->DictionaryWord->reserve($dictionaryId,$deviceCount);
            if($dictionaryWords === false)
                throw new InternalErrorException('Dictionary does not contain enough free words to accomodate this formation');
            $dictionaryWords = Hash::combine($dictionaryWords,'{n}.DictionaryWord.id','{n}');
            $dictionaryWordIds = array_keys($dictionaryWords);

            //Devices data structure
            $devices = array();
            $nextDevicePsuedoId = 1;
            $tmpDictionaryWordIds = $dictionaryWordIds;
            foreach($blueprintPartCounts as $blueprintPartId => $deviceCount){
                for($x=0;$x<$deviceCount;$x++){

                    $dictionaryWordId = array_shift($tmpDictionaryWordIds);
                    $name = $dictionaryWords[$dictionaryWordId]['DictionaryWord']['word'];

                    $blueprintPart = $blueprintParts[$blueprintPartId];

                    $devices[] = array(
                        'psuedoId' => $nextDevicePsuedoId++,
                        'deviceTypeId' => $blueprintPart['DeviceType']['id'],
                        'roleId' => $blueprintPart['Role']['id'],
                        'name' => $name,
                        'blueprintPartName' => $blueprintPart['BlueprintPart']['name'],
                    );
                }
            }

            $data = array(
                'regions' => $regions,
                'flavors' => $flavors,
                'devices' => $devices,
                'dictionaryWordIds' => $dictionaryWordIds
            );

            $this->Wizard->save(null,$data,true);
        }

        $this->set($data);
    }

    public function _processConfigureDevices(){

        $this->loadModel('Device');
        $this->loadModel('Implementation');
        $this->loadModel('DictionaryWord');
        $this->loadModel('QueueJob');
        $this->loadModel('Config');

        $devices = $this->Wizard->read('_configureDevices.devices');
        $implementationId = $this->Wizard->read('formationSettings.Implementation.id');
        $defaultImageId = $this->Implementation->getDefaultImageId($implementationId);

        $regions = $this->Implementation->getRegions($implementationId);
        $regions = Hash::combine($regions,'{n}.id','{n}');

        $internalDnsSuffix = $this->Config->findByVar('dns.internal.domain');
        $internalDnsSuffix = $internalDnsSuffix['Config']['val'];

        $externalDnsSuffix = $this->Config->findByVar('dns.external.domain');
        $externalDnsSuffix = $externalDnsSuffix['Config']['val'];

        //Store device data structures that will be handed to saveMany
        $deviceObjects = array();

        foreach($devices as $device){

            $psuedoId = $device['psuedoId'];
            $name = $device['name'];
            $roleId = $device['roleId'];
            $deviceTypeId = $device['deviceTypeId'];

            //Create device data structure
            $deviceObject = array(
                'Device' => array(
                    'implementation_id' => $implementationId,
                    'role_id' => $roleId,
                    'device_type_id' => $deviceTypeId,
                    'name' => $name
                ),
                'DeviceAttribute' => array(
                    array(
                        'var' => 'implementation.image_id',
                        'val' => $defaultImageId
                    )
                )
            );

            //Check if device parameters exist - page tampering
            if(!isset($this->request->data['Device'][$psuedoId]) || empty($this->request->data['Device'][$psuedoId])){
                $this->setFlash("Device parameters are missing for $name.",'error');
                return false;
            }

            $deviceParams = $this->request->data['Device'][$psuedoId];

            //Check for region
            if(!isset($deviceParams['region']) || empty($deviceParams['region'])){
                $this->setFlash("Please select a region for device $name.");
                return false;
            }
            else {

                $regionId = $deviceParams['region'];

                //Validate region
                if(!isset($regions[$regionId])){
                    $this->setFlash("Invalid region specified for device $name.");
                    return false;
                }

                $deviceObject['DeviceAttribute'][] = array(
                    'var' => 'implementation.region_id',
                    'val' => $regionId
                );

                $regionName = $regions[$regionId]['name'];
                $deviceObject['DeviceAttribute'][] = array(
                    'var' => 'dns.internal.fqdn',
                    'val' => "$name.$regionName.$internalDnsSuffix"
                );
                $deviceObject['DeviceAttribute'][] = array(
                    'var' => 'dns.external.fqdn',
                    'val' => "$name.$regionName.$externalDnsSuffix"
                );

            }

            //Check for flavor
            if(!isset($deviceParams['flavor']) || empty($deviceParams['flavor'])){
                $this->setFlash("Please select a flavor for device $name.",'error');
            }
            else {
                $deviceObject['DeviceAttribute'][] = array(
                    'var' => 'implementation.flavor_id',
                    'val' => $deviceParams['flavor']
                );
            }

            $deviceObjects[] = $deviceObject;
        }

        $formation = array(
            'Formation' => array(
                'name' => $this->Wizard->read('formationSettings.Formation.name')
            ),
            'Device' => $deviceObjects
        );

        $result = $this->Formation->saveAll($formation,array(
            'validate' => false,
            'deep' => true,
        ));

        if(!$result){
            $this->Formation->validationErrorsAsString(true);
            $this->setFlash('We encountered an error while creating your formation. ');
            return false;
        }

        //Mark dictionary words as used
        $dictionaryWordIds = $this->Wizard->read('_configureDevices.dictionaryWordIds');
        $this->DictionaryWord->markAsUsed($dictionaryWordIds);

        //Add create formation
        if(!$this->QueueJob->addJob(STRINGS_API_URL . '/Formations/create/' . $this->Formation->id)){
            $this->setFlash('We encountered an error while creating a job to build this formation.');
            return true;
        }

        return true;
    }
}
