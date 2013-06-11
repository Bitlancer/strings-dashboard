<?php

class DevicesController extends AppController
{
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
                'fields' => array(
                    'Device.id','Device.name','Formation.name','Role.name'
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
				'isInfraProviderConfigured' => $isInfraProviderConfigured
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
            $this->Session->setFlash('Device does not exist');
            $this->redirect(array('action' => 'index'));
        }

        //Build re-indexed attributes arrays
        $deviceAttributes = Hash::combine($device['DeviceAttribute'],'{n}.var','{n}.val');
        $implementationAttributes  = Hash::combine($device['Implementation']['ImplementationAttribute'],'{n}.var','{n}.val');

        $providerDetails= array(
            'provider_name' => $device['Implementation']['name'],
            'region' => $deviceAttributes['implementation.region_name'],
            'image' => 'Default',
            'flavor' => $this->Device->Implementation->getFlavorDescription($device['Implementation']['id'],$deviceAttributes['implementation.flavor_id']) 
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
            'device' => $device,
            'providerDetails' => $providerDetails,
            'deviceAddresses' => $deviceAddresses
        ));
    }
}
