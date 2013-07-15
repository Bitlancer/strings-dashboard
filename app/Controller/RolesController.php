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

        $this->DataTables->setColumns(array(
            'Name' => array(
                'model' => 'Role',
                'column' => 'name'
            )
        ));

        if($this->request->isAjax()){ //Datatables request
            
            $this->DataTables->process(array(
                'contain' => array(),
                'fields' => array(
                    'Role.*'
                )
            ));

            $this->set(array(
                'isAdmin' => $this->Auth->User('is_admin')
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
