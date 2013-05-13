<?php

class TeamsController extends AppController {

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
                'teamTableCTAEnabled' => ($this->Auth->User('can_create_user') || $this->Auth->User('is_admin'))
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

	public function create(){

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
			$this->set(array(
				'team' => $team
			));
		}
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
}

