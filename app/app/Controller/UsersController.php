<?php

class UsersController extends AppController {


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
        		return $this->redirect($this->Auth->redirectUrl());
    		}
			else
				$this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
		}

	}

	public function logout() {
    	$this->redirect($this->Auth->logout());
	}

}
