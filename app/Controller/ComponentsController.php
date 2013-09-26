<?php

class ComponentsController extends AppController
{
    public $uses = array('Module');

    public function index() {

        $this->DataTables->setColumns(array(
            'Name' => array(
                'model' => 'Module',
                'column' => 'short_name'
            )
        ));

        if($this->request->isAjax()){ //Datatables request

            $this->DataTables->process(
                array(
                    'contain' => array(),
                    'fields' => array(
                        'Module.*'
                    )
                )
            );

            $this->set(array(
                'isAdmin' => $this->Auth->User('is_admin')
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
