<?php

class FormationsController extends AppController
{

	/**
     * Home screen containing list of formation and create formation CTA
     */
    public function index() {

		//Verify this organization has setup one or more infrastructure providers
        $isInfraProviderConfigured = $this->Formation->Device->Implementation->hasOrganizationConfiguredServiceProvider($this->Auth->user('organization_id'),'infrastructure');
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
                ),
                'conditions' => array(
                    'Formation.organization_id' => $this->Auth->user('organization_id')
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

	public function view($id=null){

        $formation = $this->Application->find('first',array(
            'contain' => array(
                'Device' => array(
                    'fields' => array('id','name')
                )
            ),
            'conditions' => array(
                'Formation.id' => $id,
                'Formation.organization_id' => $this->Auth->user('organization_id')
            )
        ));

        if(empty($app)){
            $this->Session->setFlash(__('This formation does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->set(array(
            'formation' => $formation
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

}
