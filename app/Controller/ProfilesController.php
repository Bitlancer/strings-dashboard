<?php

class ProfilesController extends AppController
{
    public function index() {

        $tableColumns = array(
            'Name' => array(
                'model' => 'Profile',
                'column' => 'name'
            ),
        );

        if($this->request->isAjax()){ //Datatables request

            $findParameters = array(
                'contain' => array(),
                'fields' => array(
                    'Profile.*'
                )
            );

            $dataTable = $this->DataTables->getDataTable($tableColumns,$findParameters);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else { //First page request
            $this->set(array(
                'columnHeadings' => array_keys($tableColumns),
            ));
        }
    }
}
