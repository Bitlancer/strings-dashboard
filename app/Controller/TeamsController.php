<?php

class TeamsController extends AppController {

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

	public function index(){

		$teamTableColumns = array(
			'Name' => array( 
				'model' => 'Team',
				'column' => 'name'
			)
		);

		if($this->request->isAjax()){

			//Datatables
			$findParameters = array(
				'fields' => array(
					'Team.id','Team.name','Team.is_disabled'
				)
            );

			$dataTable = $this->DataTables->getDataTable($teamTableColumns,$findParameters);

			$this->set(array(
				'dataTable' => $dataTable,
				'isAdmin' => $this->Auth->User('is_admin')
			));
        }
        else{
            $this->set(array(
            	'teamTableColumns' => array_keys($teamTableColumns),
                'createCTADisabled' => !$this->Auth->User('is_admin') && !$this->Auth->User('can_create_user'),
            ));
        }
	}

	public function view($id=null){

        $team = $this->Team->find('first',array(
            'conditions' => array(
                'Team.id' => $id
            )
        ));

        if(empty($team)){
            $this->Session->setFlash(__('This team does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        $this->loadModel('User');

        $members = $this->User->find('all',array(
            'link' => array(
                'UserTeam' => array(
                    'Team'
                )
            ),
            'fields' => array(
                'User.id','User.name','User.full_name'
            ),
            'conditions' => array(
                'Team.id' => $id,
                'User.is_disabled' => 0,
            )
        ));

		$this->set(array(
			'team' => $team,
            'members' => $members,
            'isAdmin' => $this->Auth->User('is_admin')
		));
	}

	public function create(){

        if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            $team = array(
                'Team' => array(
                    'name' => $this->request->data['Team']['name']
                )
            );

            $validFields = array('name');
            if($this->Team->save($this->request->data,true,$validFields)){

                $teamId = $this->Team->getInsertID();

                $members = (isset($this->request->data['Team']['members']) ? $this->request->data['Team']['members'] : array());
                list($isError,$message) = $this->updateTeamMembers($teamId,$members);
                if($isError){
                    $this->Team->delete($teamId); //Rollback
                }
                else
                    $message = 'Created team ' . $this->request->data['Team']['name'];
            }
            else {
                $isError = true;
                $message = $this->Team->validationErrorsAsString();
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
                    'redirectUri' => '/Teams/view/' . $this->Team->id
                );
            }

            echo json_encode($response);         
        }
    }

	public function edit($id=null){

		$team = $this->Team->find('first',array(
			'conditions' => array(
				'Team.id' => $id
			)
		));

		if(empty($team)){
			$this->Session->setFlash(__('This team does not exist.'),'default',array(),'error');
			$this->redirect(array('action' => 'index'));
		}

		if($team['Team']['is_disabled']){
            $this->Session->setFlash(__('This team is disabled. Please re-enable this team if you would like to make changes'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

		if($this->request->is('post')){

			$this->autoRender = false;

			$isError = false;
			$message = "";

			$validFields = array('name');
			$this->Team->id = $id;
			if($this->Team->save($this->request->data,true,$validFields)){

                $members = (isset($this->request->data['Team']['members']) ? $this->request->data['Team']['members'] : array()); 

                list($isError,$message) = $this->updateTeamMembers($id,$members);
                if(!$isError)
                    $message = 'Updated team ' . $this->request->data['Team']['name'] . '.';
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

            $this->loadModel('User');

            $members = $this->User->find('all',array(
                'link' => array(
                    'UserTeam' => array(
                        'Team'
                    )
                ),
                'fields' => array(
                    'User.id','User.name','User.full_name'
                ),
                'conditions' => array(
                    'Team.id' => $id,
                    'User.is_disabled' => 0,
                )
            ));

			$this->set(array(
                'id' => $id,
				'team' => $team,
                'members' => $members
			));
		}
	}

    private function updateTeamMembers($id,$teamMembers){

        $isError = false;
        $message = "";

        $this->loadModel('User');

        $users = $this->User->find('all',array(
            'fields' => array(
                'User.id','User.name','User.is_disabled'
            ),
            'conditions' => array(
                'User.name' => $teamMembers
            )
        ));

        $userIds = array();
        $usersIndexedByName = array();
        foreach($users as $user){
            $userIds[] = $user['User']['id'];
            $usersIndexedByName[$user['User']['name']] = $user;
        }

        //Validate all users exist and are not disabled
        foreach($teamMembers as $member){
            if(!isset($usersIndexedByName[$member])){
                $isError = true;
                $message = "User $member does not exist.";
                break;
            }
            elseif($usersIndexedByName[$member]['User']['is_disabled']){
                $isError = true;
                $message = "User $member is disabled. Please re-enable this user if you would like to add him or her to this team.";
                break;
            }
            else {}
        }

        if($isError)
            return array($isError,$message);

        
        $existingMembers = $this->User->find('all',array(
            'link' => array(
                'UserTeam' => array(
                    'Team'
                )
            ),
            'fields' => array(
                'User.id'
            ),
            'conditions' => array(
                'Team.id' => $id,
                'User.is_disabled' => 0
            )
        ));

        $existingMemberIds = array();
        foreach($existingMembers as $member)
            $existingMemberIds[] = $member['User']['id'];


        $addMembers = array_diff($userIds,$existingMemberIds);
        $removeMembers = array_diff($existingMemberIds,$userIds);

        if(count($removeMembers)){
            $deleteResult = $this->Team->UserTeam->deleteAll(array(
                'team_id' => $id,
                'user_id' => $removeMembers
            ));

            if(!$deleteResult)
                return array(true,$this->Team->UserTeam->validationErrorsAsString());
        }

        if(count($addMembers)){

            $newMembers = array();
            foreach($addMembers as $memberId){
                $newMembers[] = array(
                    'team_id' => $id,
                    'user_id' => $memberId
                );
            }

            if(!$this->Team->UserTeam->saveMany($newMembers)){
                return array(true,$this->Team->UserTeam->validationErrorsAsString(true));
            }
        }

        return array($isError,$message);
    }

	public function disable($id=null){

		$team = $this->Team->find('first',array(
            'conditions' => array(
                'Team.id' => $id
            )
        ));

        if(empty($team)){
            $this->Session->setFlash(__('This team does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

		if($team['Team']['is_disabled']){
            $this->Session->setFlash(__('This team is already disabled.'),'default',array(),'warning');
            $this->redirect(array('action' => 'index'));
        }

		if($this->request->is('post')){

			$this->Team->id = $id;
			$this->Team->set('is_disabled',1);
			if($this->Team->save()){
				$this->Session->setFlash(__('This team has been disabled.'),'default',array(),'success');
				$this->redirect(array('action' => 'index'));
			}
			else {
				$message = $this->Team->validationErrorsAsString();
				$this->Session->setFlash(__($message), 'default', array(), 'error');
				$this->redirect(array('action' => 'index'));
			}
		}

		$this->set(array(
			'team' => $team
		));
	}

	public function enable($id=null){

        $team = $this->Team->find('first',array(
            'conditions' => array(
                'Team.id' => $id
            )
        ));

        if(empty($team)){
            $this->Session->setFlash(__('This team does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        if(!$team['Team']['is_disabled']){
            $this->Session->setFlash(__('This team is already enabled.'),'default',array(),'warning');
            $this->redirect(array('action' => 'index'));
        }

        if($this->request->is('post')){
            $this->Team->id = $id;
            $this->Team->set('is_disabled',0);
            if($this->Team->save()){
                $this->Session->setFlash(__('This team has been re-enabled.'),'default',array(),'success');
                $this->redirect(array('action' => 'index'));
            }
            else {
                $message = __('Unable to re-enable team. ' . $this->Team->validationErrorsAsString());
                $this->Session->setFlash(__($message), 'default', array(), 'error');
                $this->redirect(array('action' => 'index'));
            }
        }

        $this->set(array(
            'team' => $team
        ));
    }

	public function editMembers($id=null){

        $members = $this->Team->User->find('all',array(
            'link' => array(
                'Team'
            ),
            'fields' => array(
                'User.id','User.name','User.full_name'
            ),
            'conditions' => array(
                'Team.id' => $id,
				'User.is_disabled' => 0,
            )
        ));

        $this->set(array(
            'id' => $id,
            'members' => $members
        ));
    }

	public function addUser($id=null){

        $this->autoRender = false;

        $isError = false;
        $message = "";
        $memberId = 0;

        $userName = "";
        if($this->request->is('post'))
            $userName = $this->request->data['name'];
        else
            $userName = $this->request->query['name'];

        $user = $this->Team->User->find('first',array(
            'fields' => array(
                'User.id','User.name'
            ),
            'conditions' => array(
                'User.name' => $userName,
				'User.is_disabled' => 0
            )
        ));

        if(empty($user)){
            $isError = true;
            $message = 'No such user found';
        }
        else {
            $userId = $user['User']['id'];

            //Check if this user is already a member of this team
            $count = $this->Team->User->find('count',array(
                'link' => array(
                    'Team'
                ),
                'conditions' => array(
                    'Team.id' => $id,
                    'User.id' => $userId
                )
            ));
            if($count){
                $isError = true;
                $message = 'This user is already a member of this team';
            }
            else {
                if(!$this->Team->habtmAdd('User', $id, array($userId)))
                    $memberId = $userId;
                else {
                    $isError = true;
                    $message = 'Unable to add this user to this team';
                }
            }
        }

        echo json_encode(array(
            'isError' => $isError,
            'message' => __($message),
            'id' => $memberId
        ));
    }

	public function removeUser($id=null){

        $this->autoRender = false;

        $userId = 0;
        if($this->request->is('post'))
            $userId = $this->request->data['id'];
        else
            $userId = $this->request->query['id'];

        $this->Team->habtmDelete('User', $id, array($userId));
    }

    public function searchByName(){

        $this->autoRender = false;

        $search = $this->request->query['term'];

        $teams = $this->Team->find('all',array(
            'fields' => array(
                'Team.id','Team.name'
            ),
            'conditions' => array(
                'Team.is_disabled' => 0,
                'Team.name LIKE' => "%$search%",
            )
        ));

        foreach($teams as $index => $team){
            $teams[$index] = $team['Team']['name'];
        }

        echo json_encode($teams);
    }
}

