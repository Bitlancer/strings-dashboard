<?php

class ProfilesController extends AppController
{
    public function index() {

        $this->DataTables->setColumns(array(
            'Name' => array(
                'model' => 'Profile',
                'column' => 'name'
            )
        ));

        if($this->request->isAjax()){ //Datatables request

            $this->DataTables->process(
                array(
                    'contain' => array(),
                    'fields' => array(
                        'Profile.*'
                    )
                )
            );

            $this->set(array(
                'isAdmin' => $this->Auth->User('is_admin')
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
