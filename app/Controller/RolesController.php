<?php

class RolesController extends AppController
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

    public function index() {

        $tableColumns = array(
            'Name' => array(
                'model' => 'Role',
                'column' => 'name'
            ),
        );

        if($this->request->isAjax()){ //Datatables request

            $findParameters = array(
                'contain' => array(),
                'fields' => array(
                    'Role.*'
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

    public function view($roleId=null){

        $role = $this->Role->find('first',array(
            'contain' => array(
                'RoleProfile' => array(
                    'Profile'
                )
            ),
            'conditions' => array(
                'Role.id' => $roleId
            )
        ));

        if(empty($role))
            throw new NotFoundException('Role does not exist');

        $this->set(array(
            'isAdmin' => $this->Auth->User('is_admin'),
            'role' => array('Role' => $role['Role']),
            'profiles' => $role['RoleProfile']
        ));
    }
}
