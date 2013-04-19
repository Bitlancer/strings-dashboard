<?php

class UsersController extends AppController {

	public function home() {

		$userTableColumns = array(
			'Name' => array(
				'model' => 'User',
				'column' => 'name'
			)
		);

		$userTableCTAEnabled = false;

		if($this->request->isAjax()){
            $this->autoRender = false;
          	echo $this->DataTables->output('GET',$userTableColumns);
        }
        else {
            $this->set(array(
            	'userTableColumns' => array_keys($userTableColumns),
                'userTableCTAEnabled' => $this->Auth->User('can_create_user')
            ));
        }
	}

	public function create() {

		$currentUserOrgId = $this->Auth->User('organization_id');

		$this->request->data['User']['organization_id'] = $currentUserOrgId;

		if($this->request->is('post')){
			if($this->User->save($this->request->data))
				$this->Session->setFlash('Sucessfully created user');
			else
				debug($this->User->validationErrors);
		}
	}

	public function login() {

		$this->layout = 'login';
		$this->set('title_for_layout', 'Login');

		$userId = $this->Auth->user('id');

		if(!empty($userId))
			$this->redirect($this->Auth->redirectUrl());

		if($this->request->is('post')){

			//Add additional conditions to login query
			$this->Auth->authenticate['Form']['scope'] = array(
				'User.is_active' => '1',
            	'Organization.short_name' => $this->request->data['Organization']['short_name'],
				'Organization.is_active' => '1'
        	);

    		if ($this->Auth->login()) {

				if($this->request->data['User']['remember_me'] == 'on'){
					$rememberMeData = array(
						'organization' => $this->request->data['Organization']['short_name'],
						'name' => $this->request->data['User']['name'],
					);
					$this->Cookie->write('User', $rememberMeData, false, '1 year');
				}
				else {
					$this->Cookie->delete('User');
				}

        		return $this->redirect($this->Auth->redirectUrl());
    		}
			else
				$this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
		}

		$userName = "";
		$userRememberMe = false;
		$organizationShortName = "";

		if($this->Cookie->read('User')){
            $organizationShortName = $this->Cookie->read('User.organization');
            $userName = $this->Cookie->read('User.name');
            $userRememberMe = 'on';
        }

		if(isset($this->request->data['User']['name']))
			$userName = $this->request->data['User']['name'];

		if(isset($this->request->data['User']['remember_me']))
			$userRememberMe = $this->request->data['User']['remember_me'];

		if(isset($this->request->data['Organization']['short_name']))
			$organizationShortName = $this->request->data['Organization']['short_name'];

		$this->set(array(
			'userName' => $userName,
			'userRememberMe' => $userRememberMe,
			'organizationShortName' => $organizationShortName
		)); 
	}

	public function logout() {
    	$this->redirect($this->Auth->logout());
	}

}
