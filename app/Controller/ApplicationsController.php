<?php

class ApplicationsController extends AppController
{
	/**
     * Home screen containing list of applications and create application CTA
     */
    public function index() {

        $applicationTableColumns = array(
            'Name' => array(
                'model' => 'Application',
                'column' => 'name'
            )
        );

        if($this->request->isAjax()){

            //Datatables
            $findParameters = array(
                'fields' => array(
                    'Application.id','Application.name'
                ),
                'conditions' => array(
                    'Application.organization_id' => $this->Auth->user('organization_id')
                )
            );

            $dataTable = $this->DataTables->getDataTable($applicationTableColumns,$findParameters);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'applicationTableColumns' => array_keys($applicationTableColumns),
            ));
        }
    }

	public function view($id=null){

        $app = $this->Application->find('first',array(
			'contain' => array(
				'Formation' => array( 
					'fields' => array('id','name')
				)
			),
            'conditions' => array(
                'Application.id' => $id,
				'Application.organization_id' => $this->Auth->user('organization_id')
            )
        ));

        if(empty($app)){
            $this->Session->setFlash(__('This application does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->set(array(
            'application' => $app
        ));
    }

	public function create(){

		if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            //Set organization
            $this->request->data['Application']['organization_id'] = $this->Auth->User('organization_id');

            if($this->Application->save($this->request->data,true,array('organization_id','name'))){
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
                    'redirectUri' => $this->referer(array('action' => 'index'))
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
                'Application.organization_id' => $this->Auth->user('organization_id')
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

            //Have to set organization_id so multi-column validation can occur
            $this->request->data['Application']['organization_id'] = $app['Organization']['id'];

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
                'Application.organization_id' => $this->Auth->user('organization_id')
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

		$members = $this->Application->Formation->find('all',array(
            'link' => array(
                'Application'
            ),
            'fields' => array(
                'Formation.id','Formation.name'
            ),
            'conditions' => array(
                'Application.id' => $id,
                'Application.organization_id' => $this->Auth->user('organization_id'),
            )
        ));

		$this->set(array(
			'id' => $id,
			'members' => $members
		));
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

		$formation = $this->Application->Formation->find('first',array(
			'fields' => array(
				'Formation.id','Formation.name'
			),
			'conditions' => array(
				'Formation.name' => $formationName,
				'Formation.organization_id' => $this->Auth->user('organization_id')
			)
		));

		if(empty($formation)){
			$isError = true;
			$message = 'No such formation found';
		}
		else {
			$formationId = $formation['Formation']['id'];

			//Check if this formation is already a member of this application
			$count = $this->Application->Formation->find('count',array(
				'link' => array(
					'Application'
				),
				'conditions' => array(
					'Application.id' => $id,
					'Formation.id' => $formationId
				)
			));
			if($count){
				$isError = true;
				$message = 'This formation is already a member of this application';	
			}
			else {
				if(!$this->Application->habtmAdd('Formation', $id, array($formationId)))
					$memberId = $formationId;
				else {
					$isError = true;
					$message = 'Unable to add this formation to this application';
				}
			}
		}

		echo json_encode(array(
    		'isError' => $isError,
			'message' => __($message),
    		'id' => $memberId
		));
	}

	public function removeFormation($id=null){

		$this->autoRender = false;

		$formationId = 0;
        if($this->request->is('post'))
            $formationId = $this->request->data['id'];
        else
            $formationId = $this->request->query['id'];	

		$this->Application->habtmDelete('Formation', $id, array($formationId));
	}

	public function editPermissions($id=null){

       $this->loadModel('SudoRole');

        /*
       $teamsSudoRoles = $this->Application->find('all',array(
            'contain' => array(
                'TeamApplication' => array(
                    'Team',
                    'TeamApplicationSudo' => array(
                        'SudoRole'
                    )
                )
            ),
            'conditions' => array(
                'Application.id' => $id
            )
        ));

        $teamsSudoRoles = $this->Application->TeamApplication->find('all',array(
            'link' => array(
                'Team'
            ),
            'fields' => array(
                'Team.id','Team.name'
            ),
            'conditions' => array(
                'TeamApplication.application_id' => $id,
                'Team.is_disabled' => 0 
            )
        ));

        */

        $teamTableColumns = array(
            'Name' => array(
                'model' => 'Team',
                'column' => 'name'
            )
        );

        if($this->request->isAjax()){

            //Datatables
            $findParameters = array(
                'link' => array(
                    'Team'
                ),
                'fields' => array(
                    'Team.id','Team.name'
                ),
                'conditions' => array(
                    'TeamApplication.application_id' => $id,
                    'Team.is_disabled' => 0
                )
            );

            $dataTable = $this->DataTables->getDataTable($teamTableColumns,$findParameters,$this->Application->TeamApplication);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'teamTableColumns' => array_keys($teamTableColumns),
            ));
        }

	}

}
