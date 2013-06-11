<?php

class FormationsController extends AppController
{
    public function beforeFilter(){

        /*
         * Formation creation wizard settings
         */
        $this->Wizard->steps = array(
            'selectBlueprint',
            'deviceCounts',
            'formationSettings',
            'configureDevices'
        );
        $this->Wizard->lockdown = false;                     //Prevent user from navigating between steps
        $this->Wizard->nestedViews = true;                  //Store view fields within wizard/
        $this->Wizard->completeUrl = '/Formations/';        //User is redirected here after wizard completion
        $this->Wizard->cancelUrl = '/Formations/';          //User is redirected here on wizard cancellation
    }

	/**
     * Home screen containing list of formation and create formation CTA
     */
    public function index() {

		//Verify this organization has setup one or more infrastructure providers
        $this->loadModel('Implementation');
        $isInfraProviderConfigured = $this->Implementation->hasOrganizationConfiguredServiceProvider($this->Auth->user('organization_id'),'infrastructure');
        if(!$isInfraProviderConfigured){
            $this->Session->setFlash(__('Please setup an infrastructure provider <a href="#">here</a>.'),'default',array(),'error');
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
                    'Formation.id','Formation.name'
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
				'isInfraProviderConfigured' => $isInfraProviderConfigured
            ));
        }
    }

    public function searchByName(){

        $this->autoRender = false;

        $search = $this->request->query['term'];

        $formations = $this->Formation->find('all',array(
            'fields' => array(
                'Formation.id','Formation.name'
            ),
            'conditions' => array(
                'Formation.name LIKE' => "%$search%"
            )
        ));

        foreach($formations as $index => $formation){
            $formations[$index] = $formation['Formation']['name'];
        }

        echo json_encode($formations);
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
                'Service.name' => 'infrastructure'
            )
        ));

        //Get a list of dictionaries
        $dictionaries = $this->Dictionary->find('all',array('contain' => array()));

        $this->set(array(
            'implementations' => $implementations,
            'dictionaries' => $dictionaries
        ));
    }

    public function _processFormationSettings(){

        $this->loadModel('Implementation');
        $this->loadModel('Dictionary');

        //Validate implementation
        if(!isset($this->request->data['Implementation']['id']) || empty($this->request->data['Implementation']['id'])){
            $this->Session->setFlash(__('Infrastructure provider required'),'default',array(),'error');
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
            $this->Session->setFlash(__('Invalid provider supplied'),'default',array(),'error');
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

        //Get list of provider regions
        $implementationId = $this->Wizard->read('formationSettings.Implementation.id');
        $regions = $this->Implementation->getRegions($implementationId);

        //Get list of blueprint parts and associated data
        $blueprintPartCounts = $this->Wizard->read('deviceCounts.blueprintPartCounts');
        $blueprintPartIds = array_keys($blueprintPartCounts);
        $blueprintParts = $this->BlueprintPart->find('all',array(
            'contain' => array(
                'Role'
            ),
            'fields' => array(
                'Role.*','BlueprintPart.*'
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

        //Select names for each device from dictionary and mark as used
        $dictionaryId = $this->Wizard->read('formationSettings.Dictionary.id');
        $dictionaryWords = $this->DictionaryWord->find('all',array(
            'limit' => $deviceCount,
            'conditions' => array(
                'DictionaryWord.dictionary_id' => $dictionaryId,
                'DictionaryWord.status' => 0,
            )
        ));
        if(count($dictionaryWords) !== $deviceCount){
            $this->Session->setFlash(__('Could not allocate enough device names (dictionary words) for this formation'),'default',array(),'error');
            return false;
        }
        $dictionaryWords = Hash::combine($dictionaryWords,'{n}.DictionaryWord.id','{n}');
        /*
        $wordIds = array_keys($dictionaryWords);
        $this->DictionaryWord->updateAll(
            array(
                'DictionaryWord.status' => 2
            ),
            array(
                'DictionaryWord.id' => $wordIds
            )
        );
        */

        //Devices data structure
        $devices = array();
        $nextDevicePsuedoId = 1;
        $dictionaryWordIds = array_keys($dictionaryWords);
        foreach($blueprintPartCounts as $blueprintPartId => $deviceCount){
            for($x=0;$x<$deviceCount;$x++){
                $devices[] = array(
                    'psuedoId' => $nextDevicePsuedoId++,
                    'dictionaryWordId' => array_shift($dictionaryWordIds),
                    'blueprintPartId' => $blueprintPartId
                );
            }
        }

        $this->set(array(
            'regions' => $regions,
            'dictionaryWords' => $dictionaryWords,
            'blueprintParts' => $blueprintParts,
            'devices' => $devices
        ));
    }

    public function _processConfigureDevices(){

    }
}
