<?php

class DevicesController extends AppController
{
	/**
     * Home screen containing list of devices and create device CTA
     */
    public function index() {

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
                ),
                'conditions' => array(
                    'Device.organization_id' => $this->Auth->user('organization_id')
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
            ));
        }
    }
}
