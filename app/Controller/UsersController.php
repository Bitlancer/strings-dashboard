<?php

class UsersController extends AppController {

	/**
	 * Home screen containing list of users and create user CTA
	 */
	public function index() {

		$userTableColumns = array(
			'Name' => array( 
				'model' => 'User',
				'column' => 'full_name'
			)
		);

		if($this->request->isAjax()){

			//Datatables
			$findParameters = array(
				'fields' => array(
					'User.id','User.organization_id','User.name','User.full_name',
					'User.first_name','User.last_name','User.is_disabled'
				)
            );

			$dataTable = $this->DataTables->getDataTable($userTableColumns,$findParameters);

			$this->set(array(
				'dataTable' => $dataTable,
				'isAdmin' => $this->Auth->User('is_admin')
			));
        }
        else{
            $this->set(array(
            	'userTableColumns' => array_keys($userTableColumns),
                'userTableCTAEnabled' => ($this->Auth->User('can_create_user') || $this->Auth->User('is_admin'))
            ));
        }
	}

	public function view($id=null){

		$user = $this->User->find('first',array(
            'conditions' => array(
                'User.id' => $id
            )
        ));

        if(empty($user)){
            $this->Session->setFlash(__('This user does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

		$this->set(array(
			'user' => $user
		));
	}

	/**
	 * Create a new user
	 */
	public function create() {

		if($this->request->is('post')){

			$this->autoRender = false;

			$isError = false;
			$message = "";

			//Verify passwords match
			if($this->request->data['User']['password'] != $this->request->data['User']['confirm_password']){
				$isError = true;
				$message = 'Passwords do not match.';
			}
			else
			{
				unset($this->request->data['User']['confirm_password']);

				if($this->User->save($this->request->data)){
					$message = 'Created new user ' . $this->request->data['User']['name'] . '.';
				}
				else {
					$isError = true;
					$message = $this->User->validationErrorsAsString();
				}
			}

			if($isError){
				$response = array(
					'isError' => $isError,
					'message' => __($message)
				);
			}
			else {
				$this->Session->setFlash(__($message),'default',array(),'success');
				$response = array(
					'redirectUri' => $this->referer(array('action' => 'index'))
				);
			}

			echo json_encode($response);
		}
	}

	/**
	 * Edit a user
	 */
	public function edit($id=null){

		$user = $this->User->find('first',array(
			'conditions' => array(
				'User.id' => $id
			)
		));

		if(empty($user)){
			$this->Session->setFlash(__('This user does not exist.'),'default',array(),'error');
			$this->redirect(array('action' => 'index'));
		}

		if($user['User']['is_disabled']){
            $this->Session->setFlash(__('This user is disabled. Please re-enable this user if you would like to make changes to the user'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

		if($this->request->is('post')){

			$this->autoRender = false;

			$isError = false;
			$message = "";

			$validFields = array('first_name','last_name','email');
			$this->User->id = $id;
			if($this->User->save($this->request->data,true,$validFields)){
				$message = 'Updated user ' . $user['User']['name'] . '.';
			}
			else {
				$isError = true;
				$message = $this->User->validationErrorsAsString();
			}

			if($isError){
				$response = array(
					'isError' => $isError,
					'message' => __($message)
				);
			}
			else {
				$this->Session->setFlash(__($message),'default',array(),'success');
				$response = array(
					'redirectUri' => $this->referer(array('action' => 'index'))
				);
			}

			echo json_encode($response);
		}
		else {
			$this->set(array(
				'user' => $user
			));
		}
	}

	/**
	 * Allow a user to edit his or her own settings
	 */
	public function mySettings(){

		$id = $this->Auth->user('id');

		$user = $this->User->find('first',array(
            'conditions' => array(
                'User.id' => $id
            )
        ));	

		if($this->request->is('post')){

			$this->autoRender = false;

			$isError = false;
			$message = "";

			//Verify passwords match
            if($this->request->data['User']['password'] != $this->request->data['User']['confirm_password']){
				$isError = true;
                $message = 'Passwords do not match.';
            }
            else {

                unset($this->request->data['User']['confirm_password']);

				//Save user
            	$validFields = array('password','first_name','last_name','email');
            	$this->User->id = $id;
            	if($this->User->save($this->request->data,true,$validFields)){
                	$message = 'Updated your information.';
            	}
            	else {
					$isError = true;
                	$message = $this->User->validationErrorsAsString();
            	}
			}

			if($isError){
                $response = array(
                    'isError' => $isError,
                    'message' => __($message)
                );
            }
            else {
                $this->Session->setFlash(__($message),'default',array(),'success');
                $response = array(
                    'redirectUri' => $this->referer(array('action' => 'index'))
                );
            }
            echo json_encode($response);
        }
		else {
			$this->set(array(
            	'user' => $user
        	));
		}	
	}

	public function resetPassword($id=null){

		$user = $this->User->find('first',array(
            'conditions' => array(
                'User.id' => $id
            )
        ));

        if(empty($user)){
            $this->Session->setFlash(__('This user does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

		if($user['User']['is_disabled']){
			$this->Session->setFlash(__('This user is disabled. Please re-enable this user if you would like to make changes to the user'),'default',array(),'error');
			$this->redirect(array('action' => 'index'));
		}

		if($this->request->is('post')){

			$this->autoRender = false;

			$isError = false;
			$message = "";

			//Verify passwords match
            if($this->request->data['User']['password'] != $this->request->data['User']['confirm_password']){
				$isError = true;
                $message = 'Passwords do not match.';
            }
            else {

                unset($this->request->data['User']['confirm_password']);

				$this->User->id = $id;
				if($this->User->save($this->request->data,true,array('password'))){
					$message = 'Updated user password.';
				}
				else {
					$isError = true;
					$message = $this->User->validationErrorsAsString();
				}
			}

			if($isError){
                $response = array(
                    'isError' => $isError,
                    'message' => __($message)
                );
            }
            else {
                $this->Session->setFlash(__($message),'default',array(),'success');
                $response = array(
                    'redirectUri' => $this->referer(array('action' => 'index'))
                );
            }
            echo json_encode($response);	
		}
	}

	public function disable($id=null){

		$user = $this->User->find('first',array(
            'conditions' => array(
                'User.id' => $id
            )
        ));

        if(empty($user)){
            $this->Session->setFlash(__('This user does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

		if($user['User']['is_disabled']){
            $this->Session->setFlash(__('This user is already disabled.'),'default',array(),'warning');
            $this->redirect(array('action' => 'index'));
        }

		if($this->request->is('post')){

			$this->User->id = $id;
			$this->User->set('is_disabled',1);
			if($this->User->save()){
				$this->Session->setFlash(__('This user has been disabled.'),'default',array(),'success');
				$this->redirect(array('action' => 'index'));
			}
			else {
				$message = $this->User->validationErrorsAsString();
				$this->Session->setFlash(__($message), 'default', array(), 'error');
				$this->redirect(array('action' => 'index'));
			}
		}

		$this->set(array(
			'user' => $user
		));
	}

	public function enable($id=null){

        $user = $this->User->find('first',array(
            'conditions' => array(
                'User.id' => $id
            )
        ));

        if(empty($user)){
            $this->Session->setFlash(__('This user does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        if(!$user['User']['is_disabled']){
            $this->Session->setFlash(__('This user is already enabled.'),'default',array(),'warning');
            $this->redirect(array('action' => 'index'));
        }

        if($this->request->is('post')){
            $this->User->id = $id;
            $this->User->set('is_disabled',0);
            if($this->User->save()){
                $this->Session->setFlash(__('This user has been re-enabled.'),'default',array(),'success');
                $this->redirect(array('action' => 'index'));
            }
            else {
                $message = __('Unable to re-enable user. ' . $this->User->validationErrorsAsString());
                $this->Session->setFlash(__($message), 'default', array(), 'error');
                $this->redirect(array('action' => 'index'));
            }
        }

        $this->set(array(
            'user' => $user
        ));
    }

	public function login() {

		$this->layout = 'login';
		$this->set('title_for_layout', 'Login');

		$userId = $this->Auth->user('id');

		if(!empty($userId))
			$this->redirect($this->Auth->redirectUrl());

		if($this->request->is('post')){

			//Detach OrganizationOwned behavior
			$this->User->Behaviors->unload('OrganizationOwned');	

			//Add additional conditions to login query
			$this->Auth->authenticate['Form']['scope'] = array(
				'User.is_disabled' => '0',
				'Organization.is_disabled' => '0',
            	'Organization.short_name' => $this->request->data['Organization']['short_name']
        	);

    		if($this->Auth->login()) {

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

