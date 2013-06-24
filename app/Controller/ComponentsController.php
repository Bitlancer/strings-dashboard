<?php

class ComponentsController extends AppController
{
    public $uses = array('Module');

    public function index() {

        $tableColumns = array(
            'Name' => array(
                'model' => 'Module',
                'column' => 'short_name'
            ),
        );

        if($this->request->isAjax()){ //Datatables request

            $findParameters = array(
                'contain' => array(),
                'fields' => array(
                    'Module.*'
                )
            );

            $dataTable = $this->DataTables->getDataTable($tableColumns,$findParameters,$this->Module);

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

    public function view($moduleId=null){

        $module = $this->Module->find('first',array(
            'contain' => array(
                'ModuleSource'
            ),
            'conditions' => array(
                'Module.id' => $moduleId
            )
        ));

        if(empty($module))
            throw new NotFoundException('Module not found');

        $this->set(array(
            'module' => $module
        ));
    }
}
