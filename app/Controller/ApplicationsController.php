<?php

class ApplicationsController extends AppController
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
     * Home screen containing list of applications and create application CTA
     */
    public function index() {

        $this->DataTables->setColumns(array(
            'Name' => array(
                'model' => 'Application',
                'column' => 'name'
            )
        ));

        if($this->request->isAjax()){

            $this->DataTables->process(
                array(
                    'contain' => array(),
                    'fields' => array('Application.*')
                ),
                $this->Application
            );

            $this->set(array(
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'createCTADisabled' => !$this->Auth->User('is_admin'),
            ));
        }
    }

	public function view($id=null){

        $app = $this->Application->find('first',array(
			'contain' => array(
                'ApplicationFormation' => array(
				    'Formation' => array( 
					    'fields' => array('id','name')
				    )
                ),
                'TeamApplication' => array(
                    'Team' => array(
                        'conditions' => array(
                            'Team.is_disabled' => 0
                        )
                    ),
                    'TeamApplicationSudo' => array(
                        'SudoRole' => array(
                            'fields' => array('id','name')
                        )
                    )
                )
			),
            'conditions' => array(
                'Application.id' => $id,
            )
        ));

        if(empty($app)){
            $this->Session->setFlash(__('This application does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        $formations = array();
        foreach($app['ApplicationFormation'] as $appForm){
            $formations[] = array('Formation' => $appForm['Formation']);
        }

        $permissions = array();
        foreach($app['TeamApplication'] as $teamApp){
            $sudoRoles = array();
            $team = array('Team' => $teamApp['Team']);
            foreach($teamApp['TeamApplicationSudo'] as $teamAppSudo){
                $sudoRoles[] = array('SudoRole' => $teamAppSudo['SudoRole']);
            }
            $permissions[] = array_merge($team,array('SudoRole' => $sudoRoles));
        }

        $this->set(array(
            'isAdmin' => $this->Auth->User('is_admin'),
            'application' => $app,
            'formations' => $formations,
            'permissions' => $permissions,
        ));
    }

	public function create(){

		if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            if($this->Application->save($this->request->data,true,array('name'))){
            	$message = 'Created new application ' . $this->request->data['Application']['name'] . '.';
            }
            else {
                $isError = true;
                $message = $this->Application->validationErrorsAsString();
            }

            if($isError){
                $response = array(
                    'isError' => $isError,
                    'message' => __($message)
                );
            }
            else {
                $this->Session->setFlash(__($message),'default',array(),'success');
                $response = array(
                    'redirectUri' => '/Applications/view/' . $this->Application->id
                );
            }

            echo json_encode($response);
        }
	}

	/**
     * Edit an application
     */
    public function edit($id=null){

        $app = $this->Application->find('first',array(
            'conditions' => array(
                'Application.id' => $id,
            )
        ));

        if(empty($app)){
            $this->Session->setFlash(__('This application does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            $validFields = array('name');
            $this->Application->id = $id;
            if($this->Application->save($this->request->data,true,$validFields)){
                $message = 'Updated application ' . $app['Application']['name'] . '.';
            }
            else {
                $isError = true;
                $message = $this->Application->validationErrorsAsString();
            }

            if($isError){
                $response = array(
                    'isError' => $isError,
                    'message' => __($message)
                );
            }
            else {
                $this->Session->setFlash(__($message),'default',array(),'success');
                $response = array(
                    'redirectUri' => $this->referer(array('action' => 'index'))
                );
            }

            echo json_encode($response);
        }
        else {
            $this->set(array(
                'application' => $app
            ));
        }
    }

	public function delete($id=null){

        $app = $this->Application->find('first',array(
            'conditions' => array(
                'Application.id' => $id,
            )
        ));

        if(empty($app)){
            $this->Session->setFlash(__('This application does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        if($this->request->is('post')){

            $this->Application->id = $id;
            if($this->Application->delete()){
                $this->Session->setFlash(__($app['Application']['name'] . ' has been deleted.'),'default',array(),'success');
                $this->redirect(array('action' => 'index'));
            }
            else {
                $message = $this->Application->validationErrorsAsString();
                $this->Session->setFlash(__($message), 'default', array(), 'error');
                $this->redirect(array('action' => 'index'));
            }
        }

        $this->set(array(
            'application' => $app
        ));
    }

	public function editFormations($id=null){

        $application = $this->Application->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Application.id' => $id
            )
        ));

        if(empty($application))
            throw new NotFoundException('Application does not exist');

        $this->DataTables->setColumns(array(
            'Name' => array(
                'model' => 'Formation',
                'column' => 'name'
            )
        ));

        if($this->isJsonRequest()){

            $this->DataTables->process(
                array(
                    'contain' => array(
                        'Formation'
                    ),
                    'fields' => array(
                        'Formation.*'
                    ),
                    'conditions' => array(
                        'ApplicationFormation.application_id' => $id
                    )
                ),
                $this->Application->ApplicationFormation
            );

            $this->set(array(
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'application' => $application
            ));
        }
	}

	public function addFormation($id=null){

		$this->autoRender = false;

		$isError = false;
		$message = "";
		$memberId = 0;

		$formationName = "";
        if($this->request->is('post'))
            $formationName = $this->request->data['name'];
        else
            $formationName = $this->request->query['name'];

        $this->loadModel('Formation');

		$formation = $this->Formation->find('first',array(
			'fields' => array(
				'Formation.id','Formation.name'
			),
			'conditions' => array(
				'Formation.name' => $formationName,
			)
		));

		if(empty($formation)){
			$isError = true;
			$message = 'No such formation found';
		}
		else {
			$formationId = $formation['Formation']['id'];

			//Check if this formation is already a member of this application
			$count = $this->Application->ApplicationFormation->find('count',array(
				'conditions' => array(
					'ApplicationFormation.application_id' => $id,
					'ApplicationFormation.formation_id' => $formationId
				)
			));
			if(!$count){
                $appFormation = array(
                    'ApplicationFormation' => array(
                        'application_id' => $id,
                        'formation_id' => $formationId
                    )
                );
				if($this->Application->ApplicationFormation->save($appFormation))
					$memberId = $formationId;
				else {
					$isError = true;
					$message = 'Unable to add this formation to this application';
                    $message = $this->Application->ApplicationFormation->validationErrorsAsString();
				}
			}
		}

		echo json_encode(array(
    		'isError' => $isError,
			'message' => __($message),
    		'id' => $memberId
		));
	}

	public function removeFormation($applicationId=null){

		$this->autoRender = false;

		$formationId = 0;
        if($this->request->is('post'))
            $formationId = $this->request->data['id'];
        else
            $formationId = $this->request->query['id'];	

		$this->Application->ApplicationFormation->deleteAll(
            array(
                'ApplicationFormation.application_id' => $applicationId,
                'ApplicationFormation.formation_id' => $formationId
            ),
            true    //Cascade
        );
	}

    public function deploy($id=null){

    }

    public function dns($applicationId=null) {

        $this->loadModel('DeviceDns');

        $this->DataTables->setColumns(
            array(
                'Device' => array(
                    'model' => 'Device',
                    'column' => 'name'
                ),
                'DNS Record' => array(
                    'model' => 'DeviceDns',
                    'column' => 'name'
                )
            )
        );
        
        if($this->request->isAjax()){

            $this->DataTables->process(
                array(
                    'contain' => array(
                        'Device',
                        'ApplicationFormation'
                    ),
                    'fields' => array(
                        'DeviceDns.*','Device.*'
                    ),
                    'conditions' => array(
                        'ApplicationFormation.application_id' => $applicationId
                    )
                ),
                $this->DeviceDns
            );
        }
    }

    public function manageDnsRecords($applicationId=null){

        //Get the application and its associated devices
        $application = $this->Application->find('first',array(
            'contain' => array(
                'ApplicationFormation' => array(
                    'Formation' => array(
                        'Device' => array(
                            'DeviceAttribute',
                            'conditions' => array(
                                'Device.status <>' => 'active'
                            )
                        )
                    )
                )
            ),
            'conditions' => array(
                'Application.id' => $applicationId
            )
        ));

        if(empty($application))
            throw new NotFoundException('Application does not exist.');

        //Get devices
        $devices = array();
        foreach($application['ApplicationFormation'] as $appForm){
            $formation = $appForm['Formation'];
            foreach($formation['Device'] as $device)
                $devices[] = $device;
        }

        $this->set(array(
            'application' => $application,
            'devices' => $devices
        ));
    }

    public function manageDeviceDnsRecords($applicationId=null,$deviceId=null){

        $this->loadModel('DeviceDns');

        $this->DataTables->setColumns(
            array(
                'Record' => array(
                    'model' => 'DeviceDns',
                    'column' => 'name'
                )
            )
        );

        if($this->request->isAjax()){

            $this->DataTables->process(
                array(
                    'contain' => array(
                        'ApplicationFormation'
                    ),
                    'conditions' => array(
                        'ApplicationFormation.application_id' => $applicationId,
                        'DeviceDns.device_id' => $deviceId
                    )
                ),
                $this->DeviceDns
            );
        }
    }

    public function addDnsRecord($applicationId=null,$deviceId=null){

        $this->loadModel('DeviceDns');
        $this->loadModel('Device');
        $this->loadModel('Config');

        $this->autoRender = false;

        $isError = false;
        $message = "";
       
        if(!isset($this->request->data['name'])){
            $isError = true;
            $message = "Please enter a hostname.";
        }
        else {
            $hostname = isset($this->request->data['name']) ? $this->request->data['name'] : "";

            $appForm = $this->Application->ApplicationFormation->find('first',array(
                'link' => array(
                    'Application',
                    'Formation' => array(
                        'Device'
                    )
                ),
                'fields' => array(
                    'ApplicationFormation.*',
                    'Application.*',
                    'Device.*'
                ),
                'conditions' => array(
                    'ApplicationFormation.application_id' => $applicationId,
                    'Device.id' => $deviceId
                )
            ));

            if(empty($appForm)){
                $isError = true;
                $message = 'This device does not exist or is not associated with this application.';
            }
            else {

                //Get the device
                $device = $this->Device->find('first',array(
                    'contain' => array(
                        'DeviceAttribute'
                    ),
                    'conditions' => array(
                        'Device.id' => $deviceId
                    )
                ));
                //This should never occur
                if(empty($device))
                    throw new NotFoundException('Device does not exist.');

                $deviceAttributes = Hash::combine($device['DeviceAttribute'],'{n}.var','{n}.val');
                $implementationId = $device['Device']['implementation_id'];
                $regionId = $deviceAttributes['implementation.region_id'];

                //Determine FQDN
                $hostname = strtolower($hostname);
                $appName = $this->dnsSafeName($appForm['Application']['name']);
                $datacenter = $this->dnsSafeName(
                    $this->Device->Implementation->getRegionName($implementationId,$regionId)
                );
                $tld = $this->Config->findByVar('dns.internal.domain');
                $tld = $tld['Config']['val'];

                $fqdn = "$hostname.$appName.$datacenter.$tld";

                $deviceDns = array('DeviceDns' => array(
                    'application_formation_id' => $appForm['ApplicationFormation']['id'],
                    'device_id' => $deviceId,
                    'name' => $fqdn
                ));

                $this->DeviceDns->create();
                if(!$this->DeviceDns->save($deviceDns)){
                    $isError = true;
                    $message = $this->DeviceDns->validationErrorsAsString();
                }

            }
        }

        echo json_encode(array(
            'isError' => $isError,
            'message' => __($message)
        )); 

    }

    private function dnsSafeName($string){

        //Replace spaces with _underscores
        $string = str_replace(' ','_',$string);

        //Lowercase
        $string = strtolower($string);

        return $string;
    }

    public function removeDnsRecord() {

        $this->loadModel('DeviceDns');

        $this->autoRender = false;

        $isError = false;
        $message = "";

        $deviceDnsId = isset($this->request->data['id']) ? $this->request->data['id'] : 0;

        $result = $this->DeviceDns->deleteAll(array(
            'DeviceDns.id' => $deviceDnsId
        ));

        if(!$result){
            $isError = true;
            $message = 'Unable to remove record.';
        }

        if($isError){
            echo json_encode(array(
                'isError' => $isError,
                'message' => __($message)
            ));
        }
        else {
            echo json_encode(null);
        }
    }
}
