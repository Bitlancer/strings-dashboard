<?php

class DevicesController extends AppController
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
     * Home screen containing list of devices and create device CTA
     */
    public function index() {

		//Verify this organization has setup one or more infrastructure providers
		$isInfraProviderConfigured = $this->Device->Implementation->hasOrganizationConfiguredServiceProvider($this->Auth->user('organization_id'),'infrastructure');
		if(!$isInfraProviderConfigured){
			$this->Session->setFlash(__('Please setup an infrastructure provider <a href="#">here</a>.'),'default',array(),'error');
		}

        $deviceTableColumns = array(
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
        );

        if($this->request->isAjax()){

            //Datatables
            $findParameters = array(
                'contain' => array(
                    'Formation',
                    'Role'
                ),
                'fields' => array(
                    'Device.*','Formation.*','Role.name'
                )
            );

            $dataTable = $this->DataTables->getDataTable($deviceTableColumns,$findParameters);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'deviceTableColumns' => array_keys($deviceTableColumns),
                'createCTADisabled' => true, //!$isInfraProviderConfigured || !$this->Auth->User('is_admin'),
            ));
        }
    }

    public function view($id=null){

        $device = $this->Device->find('first',array(
            'contain' => array(
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

        if(empty($device)){
            $this->setFlash('Device does not exist');
            $this->redirect(array('action' => 'index'));
        }

        //Build re-indexed attributes arrays
        $deviceAttributes = Hash::combine($device['DeviceAttribute'],'{n}.var','{n}.val');
        $implementationAttributes  = Hash::combine($device['Implementation']['ImplementationAttribute'],'{n}.var','{n}.val');

        $implementationId = $device['Implementation']['id'];

        $providerDetails= array(
            'provider_name' => $device['Implementation']['name'],
            'region' => $this->Device->Implementation->getRegionName($implementationId,$deviceAttributes['implementation.region_id']),
            'image' => 'Default',
            'flavor' => $this->Device->Implementation->getFlavorDescription($implementationId,$deviceAttributes['implementation.flavor_id'])
        );

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
            'isAdmin' => $this->Auth->User('is_admin'),
            'device' => $device,
            'providerDetails' => $providerDetails,
            'deviceAddresses' => $deviceAddresses
        ));
    }

    public function create($id=null){

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

    protected function redirectIfNotActive($device){
    
        if($device['Device']['status'] != 'active'){
            $this->setFlash('This device is currently undergoing another operation. It cannot be modified until the previous operation completes.');
            $this->redirect($this->referer(array('action' => 'index')));
        }
    }
}
