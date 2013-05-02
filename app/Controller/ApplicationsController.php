<?php

class ApplicationsController extends AppController
{
	/**
     * Home screen containing list of users and create user CTA
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
                    'organization_id =' => $this->Auth->user('organization_id')
                )
            );

            $dataTable = $this->DataTables->getDataTable($applicationTableColumns,$findParameters);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else{
            $this->set(array(
                'applicationTableColumns' => array_keys($applicationTableColumns),
            ));
        }
    }
}
