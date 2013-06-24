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

    public function view($profileId=null){

        $profile = $this->Profile->find('first',array(
            'contain' => array(
                'ProfileModule' => array(
                    'Module'
                )
            ),
            'conditions' => array(
                'Profile.id' => $profileId
            )
        ));

        if(empty($profile))
            throw new NotFoundException('Profile does not exist');

        $this->set(array(
            'isAdmin' => $this->Auth->User('is_admin'),
            'profile' => array('Profile' => $profile['Profile']),
            'modules' => $profile['ProfileModule']
        ));
    }

}
