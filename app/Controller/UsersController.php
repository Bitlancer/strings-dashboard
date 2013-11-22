<?php

App::uses('CakeEmail', 'Network/Email');

class UsersController extends AppController {

    public function beforeFilter(){

        parent::beforeFilter();

        $this->Auth->allow(array(
            'login',
            'register',
            'forgotPassword',
            'resetPassword',
        ));
    }

    /**
     * Authorization logic
     */
    public function isAuthorized($user){

        if(parent::isAuthorized($user))
            return true;

        switch($this->action){
            case 'index':
            case 'view':
            case 'logout':
                return true;
            case 'changePassword':
            case 'sshKeys':
            case 'addSshKey':
            case 'removeSshKey':
                $userId = $this->request->pass[0];
                if($userId == $user['id'])
                    return true;
        }

        return false;
    }

	/**
	 * Home screen containing list of users and create user CTA
	 */
	public function index() {

        $this->DataTables->setColumns(array(
            'Name' => array(
                'model' => 'User',
                'column' => 'full_name'
            )
        ));

        if($this->request->isAjax()){ //Datatables request

            $this->DataTables->process(array(
                'contain' => array(),
                'field' => array(
                    'User.*'
                )
            ));

            $this->set(array(
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else{
            $this->set(array(
                'createCTADisabled' => !$this->Auth->User('is_admin') && !$this->Auth->User('can_create_user'),
            ));
        }
	}

	public function view($id=null){

		$user = $this->User->find('first',array(
            'conditions' => array(
                'User.id' => $id
            )
        ));

        if(empty($user))
            throw new NotFoundException('User does not exist.');

		$this->set(array(
			'user' => $user,
            'isAdmin' => $this->Auth->User('is_admin')
		));
	}

	/**
	 * Create a new user
	 */
	public function create() {

		if($this->request->is('post')){

			$isError = false;
			$message = "";
            $redirectUri = false;

            //Set random password initially. User will be sent a reset link.
            $this->request->data['User']['password'] = $this->generateRandomString(20);

            $validFields = array('name','password','first_name','last_name','email','is_admin');
            if($this->User->save($this->request->data,true,$validFields)){

                $user = $this->User->find('first',array(
                    'contain' => array(
                        'Organization'
                    ),
                    'conditions' => array(
                        'User.id' => $this->User->id
                    )
                ));

                $resetToken = $this->generateAndSetResetToken($user);
                $this->sendSetPasswordEmail($user, $resetToken);
                $redirectUri = $this->referer(array('action' => 'index'));
            }
            else {
                $isError = true;
                $message = $this->User->validationErrorsAsString();
            }

            $this->outputAjaxFormResponse($message,$isError,$redirectUri);
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

		if(empty($user))
            throw new NotFoundException('User does not exist.');

        $this->redirectIfDisabled($user);

		if($this->request->is('post')){

			$isError = false;
			$message = "";
            $redirectUri = false;

			$validFields = array('first_name','last_name','email','is_admin');
			$this->User->id = $id;
			if(!$this->User->save($this->request->data,true,$validFields)){
				$isError = true;
				$message = $this->User->validationErrorsAsString();
			}
            else {
                $message = 'User has been updated.';
                $redirectUri = $this->referer(array('action' => 'index'));
            }

            $this->outputAjaxFormResponse($message, $isError, $redirectUri);
		}
		else {
			$this->set(array(
				'user' => $user
			));
		}
	}

	public function changePassword($id=null){

		$user = $this->User->find('first',array(
            'conditions' => array(
                'User.id' => $id
            )
        ));

        if(empty($user))
            throw new NotFoundException('User does not exist.');

        $this->redirectIfDisabled($user);

		if($this->request->is('post')){

			$isError = false;
			$message = "";
            $redirectUri = false;

			//Verify passwords match
            if($this->request->data['User']['password'] != $this->request->data['User']['confirm_password']){
				$isError = true;
                $message = 'Passwords do not match.';
            }
            else {

                unset($this->request->data['User']['confirm_password']);

				$this->User->id = $id;
				if($this->User->save($this->request->data,true,array('password'))){
					$message = 'User password updated.';
                    $redirectUri = $this->referer(array('action' => 'index'));
				}
				else {
					$isError = true;
					$message = $this->User->validationErrorsAsString();
				}
			}

            $this->outputAjaxFormResponse($message, $isError, $redirectUri);
		}
	}

	public function disable($id=null){

		$user = $this->User->find('first',array(
            'conditions' => array(
                'User.id' => $id
            )
        ));

        if(empty($user))
            throw new NotFoundException('User does not exist.');

        $this->redirectIfDisabled($user);

		if($this->request->is('post')){

			$this->User->id = $id;
			$this->User->set('is_disabled',1);
			if($this->User->save()){
				$this->setFlash('User has been disabled.','success');
				$this->redirect($this->referer(array('action' => 'index')));
			}
			else {
				$message = $this->User->validationErrorsAsString();
				$this->setFlash($message);
				$this->redirect($this->referer(array('action' => 'index')));
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

        if(empty($user))
            throw new NotFoundException('User does not exist');

        if($this->request->is('post')){
            $this->User->id = $id;
            $this->User->set('is_disabled',0);
            if($this->User->save()){
                $this->setFlash('User has been re-enabled.','success');
                $this->redirect($this->referer(array('action' => 'index')));
            }
            else {
                $message = 'Failed to re-enable user. ' . $this->User->validationErrorsAsString();
                $this->setFlash($message);
                $this->redirect($this->referer(array('action' => 'index')));
            }
        }

        $this->set(array(
            'user' => $user
        ));
    }

    public function sshKeys($userId=null){

        $user = $this->User->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'User.id' => $userId
            )
        ));

        if(empty($user))
            throw NotFoundException('User does not exist.');

        $this->redirectIfDisabled($user);

        $this->DataTables->setColumns(array(
            'Name' => array(
                'model' => 'UserKey',
                'column' => 'name'
            )
        ));

        if($this->request->isAjax()){ //Datatables request

            $this->DataTables->process(
                array(
                    'fields' => array(
                        'UserKey.*',
                    ),
                    'conditions' => array(
                        'UserKey.user_id' => $userId
                    )
                ),
                $this->User->UserKey
            );

            $this->set(array(
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else{ //First load
            $this->set(array(
                'user' => $user,
            ));
        }
    }

    public function addSshKey($userId=null){

        $user = $this->User->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'User.id' => $userId
            )
        ));

        if(empty($user))
            throw NotFoundException('User does not exist.');

        $this->redirectIfDisabled($user);

        if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            $this->request->data['UserKey']['user_id'] = $userId;
            $validFields = array('user_id','name','public_key');
            if($this->User->UserKey->save($this->request->data,true,$validFields)){
              $message = 'Added new SSH key.';    
            }
            else {
                $isError = true;
                $message = 'Failed to add SSH key. ' . $this->User->UserKey->validationErrorsAsString();
            }

            if($isError){
                $response = array(
                    'isError' => $isError,
                    'message' => __($message)
                );
            }
            else {
                $this->setFlash($message,'success');
                $response = array(
                    'redirectUri' => $this->referer(array('action' => 'index'))
                );
            }
            echo json_encode($response); 
        }
    }

    public function removeSshKey($userId=null){

        $this->autoRender = false;
        
        $isError = false;
        $message = "";

        $user = $this->User->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'User.id' => $userId
            )
        ));

        if(empty($user))
            throw NotFoundException('User does not exist.');

        $this->redirectIfDisabled($user);

        if($this->request->is('post')){

            if(!isset($this->request->data['id']) || empty($this->request->data['id'])){
                $isError = true;
                $message = 'A ssh key id was not specified.';
            }
            else {
                $keyId = $this->request->data['id'];

                $result = $this->User->UserKey->deleteAll(
                    array(
                        'UserKey.user_id' => $userId,
                        'UserKey.id' => $keyId
                    ),
                    true,
                    true
                );

                if(!$result){
                    $isError = true;
                    $message = 'Failed to delete key. ' . $this->User->UserKey->validationErrorsAsString();
                }
            }
        }

        $response = array(
            'isError' => $isError,
            'message' => __($message)
        );

        echo json_encode($response);
    }

    public function redirectIfDisabled($user){

        if($user['User']['is_disabled']){
            $this->setFlash('This user is disabled. You must re-enable his or her account to make any changes.');
            $this->redirect(array('action' => 'index'));
        }
    }

	public function login() {

		$this->layout = 'login';
		$this->set('title_for_layout', 'Login');

        //Redirect the user if he or she is already logged in
		$userId = $this->Auth->user('id');
		if(!empty($userId))
			$this->redirect($this->Auth->redirectUrl());

        $organization = "";
        $username = "";
        $rememberMe = false;

		if($this->request->is('post')){

            $rememberMe = $this->request->data('User.remember_me');
            $username = $this->request->data('User.name');
            $organization = $this->request->data('Organization.short_name');

            if(empty($username) || empty($organization)){
                $this->setFlash('The username or password you entered is incorret.');
            }
            else {

                //Detach OrganizationOwned behavior
                $this->User->Behaviors->unload('OrganizationOwned');

                //Add additional conditions to login query
                $this->Auth->authenticate['Sha1']['scope'] = array(
                    'User.is_disabled' => '0',
                    'Organization.is_disabled' => '0',
                    'Organization.short_name' => $organization
                );

                if($this->Auth->login()) {

                    if($rememberMe == 'on'){
                        $rememberMeData = array(
                            'organization' => $organization,
                            'name' => $username,
                        );
                        $this->Cookie->write('User', $rememberMeData, false, '1 year');
                    }
                    else {
                        $this->Cookie->delete('User');
                    }

                    $this->resetFailedLoginAttempts(array(
                        'User' => array(
                            'id' => $this->Auth->User('id'),
                            'organization_id' => $this->Auth->User('organization_id')
                        )
                    ));

                    return $this->redirect($this->Auth->redirectUrl());
                }
                else {
                    $this->upFailedLoginsMaybeLockAccount($organization, $username);
                    $this->setFlash('The username or password you entered is incorrect.');
                }
            }
		}

		if($this->Cookie->read('User')){
            $organization = $this->Cookie->read('User.organization');
            $username = $this->Cookie->read('User.name');
            $rememberMe = 'on';
        }

		$this->set(array(
			'userName' => $username,
			'userRememberMe' => $rememberMe,
			'organizationShortName' => $organization
		)); 
	}

    private function resetFailedLoginAttempts($user){

        return $this->User->UserAttribute->saveAttribute($user,'strings.failed_login_attempts',0);
    }

    private function upFailedLoginsMaybeLockAccount($organization, $username){

        if(empty($organization) || empty($username))
            return;

        if($this->User->Behaviors->loaded('OrganizationOwned'))
            $this->User->Behaviors->unload('OrganizationOwned');

        if($this->User->UserAttribute->Behaviors->loaded('OrganizationOwned'))
            $this->User->UserAttribute->Behaviors->unload('OrganizationOwned');

        $user = $this->User->find('first',array(
            'contain' => array(
                'Organization',
                'UserAttribute' => array(
                    'conditions' => array(
                        'UserAttribute.var' => 'strings.failed_login_attempts'
                    )
                )
            ),
            'conditions' => array(
                'User.name' => $username,
                'Organization.short_name' => $organization,
            )
        ));

        if(!empty($user)){

            $failedLoginAttempts = 0;
            if(isset($user['UserAttribute']) && !empty($user['UserAttribute']))
                $failedLoginAttempts = $user['UserAttribute'][0]['val'];
            $failedLoginAttempts++;

            $this->User->UserAttribute->saveAttribute($user,
                                                    'strings.failed_login_attempts',
                                                    $failedLoginAttempts);

            if(($failedLoginAttempts % MAX_ALLOWED_LOGIN_ATTEMPTS) == 0){
                $this->lockAccount($user);
            }
        }
    }

    private function lockAccount($user){

        if(!isset($user['User']) || !isset($user['User']['id'])){
            throw InvalidArgumentException();
        }

        $this->User->id = $user['User']['id'];
        $this->User->saveField('password',$this->generateRandomString(20));
        
        $this->sendLockedAccountEmail($user);
    }

    private function sendLockedAccountEmail($user) {

        if(!isset($user['User']) || !isset($user['Organization']) ||
            !isset($user['User']['email']) ||
            !isset($user['Organization']['short_name'])){

            throw new InvalidArgumentException();
        }

        $subject = 'Bitlancer Strings - Account locked';

        $mail = new CakeEmail();
        $mail->config('default');
        $mail->emailFormat('text');
        $mail->template('account_locked','default');
        $mail->to($user['User']['email']);
        $mail->viewVars(array(
            'organization' => $user['Organization']['short_name'],
        ));
        $mail->subject($subject);
        $mail->send();
    }

	public function logout() {
    	$this->redirect($this->Auth->logout());
	}

    public function forgotPassword(){

        //Detach OrganizationOwned behavior
        $this->User->Behaviors->unload('OrganizationOwned');
        $this->User->UserAttribute->Behaviors->unload('OrganizationOwned');

        if($this->request->is('ajax')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            if(!isset($this->request->data['email']) || empty($this->request->data['email'])){
                $isError = true;
                $message = 'Please enter your email address.';
            }
            elseif(!isset($this->request->data['organization']) || empty($this->request->data['organization'])){
                $isError = true;
                $message = 'Please enter your organization name.';
            }
            else {

                $email = $this->request->data['email'];
                $organization = $this->request->data['organization'];

                $user = $this->User->find('first',array(
                    'fields' => array(
                        'User.id','User.organization_id','User.name',
                    ),
                    'conditions' => array(
                        'or' => array(
                            'Organization.short_name' => $organization,
                            'Organization.name' => $organization
                        ),
                        'User.email' => $email,
                        'User.is_disabled' => 0
                    )
                ));

                $isError = false;
                $message = "If your account exists, a link to reset your " .
                            "password has been emailed to $email.";

                if(!empty($user)){
                    $resetToken = false;
                    try {
                        $resetToken = $this->generateAndSetResetToken($user);
                    }
                    catch(Exception $e){
                        $isError = true;
                        $message = 'We encountered an unexpected error.';
                    }

                    if(!$isError){
                        $this->sendForgotPasswordEmail($email, $resetToken);
                    }
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
                    'redirectUri' => '/login'
                );
            }

            echo json_encode($response);
        }
        else {
            $this->redirect(array('action' => 'login'));
        }
    }

    private function generateAndSetResetToken($user){

        $resetToken = $this->generateResetToken($user);
        if(!$this->User->UserAttribute->saveAttribute($user, 'strings.reset_token', $resetToken)){
            throw new Exception("Failed to set reset token.");
        }
    
        return $resetToken;
    }

    private function sendSetPasswordEmail($user, $resetToken){

        if(!isset($user['User']) || !isset($user['Organization']) ||
            !isset($user['User']['email']) || !isset($user['User']['name']) ||
            !isset($user['Organization']['short_name'])){

            throw new InvalidArgumentException('User email, name and Organziation short_name are required');
        }

        $subject = 'Bitlancer Strings - Set your password';
        $resetLink = $this->getResetLink($resetToken);

        $mail = new CakeEmail();
        $mail->config('default');
        $mail->emailFormat('text');
        $mail->template('welcome','default');
        $mail->to($user['User']['email']);
        $mail->viewVars(array(
            'name' => $user['User']['first_name'],
            'organization' => $user['Organization']['short_name'],
            'username' => $user['User']['name'],
            'setPasswordLink' => $resetLink
        ));
        $mail->subject($subject);
        $mail->send();
    }

    private function sendForgotPasswordEmail($recipient, $resetToken){

        $subject = 'Bitlancer Strings - Password reset request';
        $resetLink = $this->getResetLink($resetToken);

        $mail = new CakeEmail();
        $mail->config('default');
        $mail->emailFormat('text');
        $mail->template('forgot_password','default');
        $mail->to($recipient);
        $mail->viewVars(array(
            'resetLink' => $resetLink
        ));
        $mail->subject($subject);
        $mail->send();
    }

    private function getResetLink($token){

        return 'https://' . $_SERVER['HTTP_HOST'] . "/Users/resetPassword?token=$token";
    }

    public function resetPassword(){

        $this->layout = 'login';

        //Detach OrganizationOwned behavior
        $this->User->Behaviors->unload('OrganizationOwned');
        $this->User->UserAttribute->Behaviors->unload('OrganizationOwned');  

        $token = $this->request->is('post') ? $this->request->data['token'] : $this->request->query['token'];

        if($this->request->is('ajax')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            $user = $this->User->UserAttribute->find('first',array(
                'contain' => array(
                    'User'
                ),
                'conditions' => array(
                    'UserAttribute.var' => 'strings.reset_token',
                    'UserAttribute.val' => $token
                )
            ));

            if(empty($user)){
                $isError = true;
                $message = 'Invalid token.';
            }
            elseif(strtotime($user['UserAttribute']['updated']) <= strtotime('-4 hours')){

                $this->User->UserAttribute->delete($user['UserAttribute']['id']);

                $isError = true;
                $message = 'Your token has expired.';
            }
            else {

                if($this->request->data['password'] != $this->request->data['confirmPassword']){
                    $isError = true;
                    $message = 'Passwords do not match.';
                }
                else {

                    $this->User->id = $user['User']['id'];
                    if($this->User->saveField('password',$this->request->data['password'],array('validate' => true))){
                        $message = 'Your password has been updated.';
                        $this->User->UserAttribute->delete($user['UserAttribute']['id']);
                        $this->resetFailedLoginAttempts($user);
                    }
                    else {
                        $isError = true;
                        $message = "Failed to update your password. " . $this->User->validationErrorsAsString();
                    }
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
                    'redirectUri' => '/login'
                );
            }

            echo json_encode($response);
        }

        $this->set(array(
            'token' => $token
        ));
    }
    
    /**
     * Generate a reset token
     */
    private function generateResetToken($user){

        if(!isset($user['User']) || !isset($user['User']['name']) ||
            !isset($user['User']['organization_id'])){

            throw new InvalidArgumentException('User array must contain name and organization_id');
        }

        $str = $user['User']['organization_id'] . "|" .
                $user['User']['name'] . "|" .
                $this->generateRandomString(20);

        return sha1($str);
    }

    private function generateRandomString($length){

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $numChars = strlen($chars) - 1;

        $token = "";
        for($x=0;$x<$length;$x++){
            $token .= $chars[mt_rand(0,$numChars)];
        }

        return $token;
    }

    public function register(){

        if($this->request->is('ajax')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            if(!isset($this->request->data['email']) || empty($this->request->data['email'])){
                $isError = true;
                $message = "Please enter a valid email address.";
            }
            else {

                $email = $this->request->data['email'];

                $mail = new CakeEmail();
                $mail->config('default');
                $mail->to(CRM_EMAIL);
                $mail->subject('Customer Interest in Strings');
                $mail->send("The customer below has expressed interest in Strings.\r\n\r\n Email: $email\r\n");

                $message = "You're registered!";
            }

            $response = array(
                'isError' => $isError,
                'message' => __($message)
            );

            echo json_encode($response);
        }
    }

    public function search(){

        $this->autoRender = false;

        $search = $this->request->query['term'];

        $users = $this->User->find('all',array(
            'fields' => array(
                'User.id','User.name'
            ),
            'conditions' => array(
                'User.is_disabled' => 0,
                'OR' => array(
                    'User.name LIKE' => "%$search%",
                    'User.full_name LIKE' => "%$search%"
                )
            )
        ));

        foreach($users as $index => $user){
            $users[$index] = $user['User']['name'];
        }

        echo json_encode($users);
    }

}

