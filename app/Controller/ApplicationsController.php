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

            if($this->Application->save($this->request->data)){
            	$message = 'Created new application ' . $this->request->data['Application']['name'] . '.';
            }
            else {
                $isError = true;
                $message = $this->Application->validationErrorsAsString();
            }

            if($isError){
                $response = array(
                    'isError' => $isError,
                    'message' => __('Failed: ' . $message)
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
                    'message' => __('Failed: ' . $message)
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

	public function edit_formations($id=null){

		$formationTableColumns = array(
            'Name' => array(
                'model' => 'Formation',
                'column' => 'name'
            )
        );

		$this->set(array(
			'applicationId' => $id,
			'formationTableColumns' => array_keys($formationTableColumns)
		));
	}

	public function edit_formations_data($id=null){

		$formationTableColumns = array(
            'Name' => array(
                'model' => 'Formation',
                'column' => 'name'
            )
        );	

        //Datatablesa
        $findParameters = array(
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
        );

        $dataTable = $this->DataTables->getDataTable($formationTableColumns,$findParameters,$this->Application->Formation);

        $this->set(array(
        	'dataTable' => $dataTable,
            'isAdmin' => $this->Auth->User('is_admin')
        ));
	}

	public function add_formation($applicationId=null,$formationId=null){

	}

	public function remove_formation($applicationId=null,$formationId=null){

	}

}
