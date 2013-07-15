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
            )
        );
	}

    public function deploy($id=null){

    }

    public function dns($id=null){

    }
}
