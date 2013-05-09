<?php

class SudoRolesController extends AppController {

	public function beforeFilter(){
		parent::beforeFilter();

		$this->set('title_for_layout', 'Sudo');
	}

	public function index() {

        $sudoTableColumns = array(
            'Name' => array(
                'model' => 'SudoRole',
                'column' => 'name'
            )
        );

        if($this->request->isAjax()){

            //Datatables
            $findParameters = array(
                'fields' => array(
                    'SudoRole.id','SudoRole.name'
                ),
                'conditions' => array(
                    'SudoRole.organization_id' => $this->Auth->user('organization_id')
                )
            );

            $dataTable = $this->DataTables->getDataTable($sudoTableColumns,$findParameters);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'sudoTableColumns' => array_keys($sudoTableColumns),
            ));
        }
    }

	public function create(){

        if($this->request->is('post')){

            $this->autoRender = false;

			$responseType = 'notice';
            $isError = false;
            $message = "";

			//For now the user cannot set runas
			$this->request->data['runas'] = 'root';


			//Validate runas and commands
			if(empty($this->request->data['runas'])){
				$isError = true;
				$message = "Please specify at least one runas user";
			}
			elseif(empty($this->request->data['commands'])){
				$isError = true;
                $message = "Please specify at least one command";
			}
			else {

				//Contains sudo attributes for save - runas & commands
				$sudoAttributes = array();

				//Process runas users
                $sudoAttribute = array(
                   	'organization_id' => $this->Auth->user('organization_id'),
                    'name' => 'sudoRunAs'
                );
				$runasUsers = array_unique(explode(",",$this->request->data['runas']));
                foreach($runasUsers as $user){
                	$sudoAttribute['value'] = $user;
					$sudoAttributes[] = $sudoAttribute;
				}

				//Process commands
                $sudoAttribute = array(
                	'organization_id' => $this->Auth->user('organization_id'),
                    'name' => 'sudoCommand'
                );
				$commands = array_unique(explode(",",$this->request->data['commands']));
                foreach($commands as $command){
                    $sudoAttribute['value'] = $command;
					$sudoAttributes[] = $sudoAttribute;
                }

				$sudoRole = array(
					'SudoRole' => array(
						'organization_id' => $this->Auth->User('organization_id'),
						'name' => $this->request->data['SudoRole']['name'],
					),
					'SudoAttribute' => $sudoAttributes
				);

				$result = $this->SudoRole->saveAssociated($sudoRole,array('validate'=>true));
				if($result){
					$message = 'Created sudo role ' . $this->request->data['SudoRole']['name'];
				}
				else {
					$isError = true;
					$message = $this->SudoRole->validationErrorsAsString(true);
				}
			}

			if($isError){
                $response = array(
                   	'isError' => $isError,
                   	'message' => __($message)
                );
            }
            else {
                $this->Session->setFlash(__($message),'default',array(),($isError ? 'error' : 'success'));
                $response = array(
                    'redirectUri' => $this->referer(array('action' => 'index'))
                );
            }

            echo json_encode($response);
        }
    }

	public function edit($id=null){
		
		$sudoRole = $this->SudoRole->find('first',array(
			'conditions' => array(
				'SudoRole.id' => $id,
				'SudoRole.organization_id' => $this->Auth->user('organization_id')
			)
		));

		$sudoAttributes = $this->SudoRole->SudoAttribute->find('all',array(
			'link' => array(
				'SudoRole'
			),
			'fields' => array(
				'SudoAttribute.name','SudoAttribute.value'
			),
			'conditions' => array(
				'SudoRole.id' => $id,
				'SudoRole.organization_id' => $this->Auth->user('organization_id')
			)
		));

		//Generate runas and commands arrays
		$runas = array();
		$commands = array();
		foreach($sudoAttributes as $attr){
			switch($attr['SudoAttribute']['name']){
				case 'sudoRunAs':
					$runas[] = $attr['SudoAttribute']['value'];
					break;
				case 'sudoCommand':
					$commands[] = $attr['SudoAttribute']['value'];
					break;
				default:
					break;
			}
		}

		if($this->request->is('post')){

			$this->autoRender = false;

			$isError = false;
			$message = "";

			$sudoRole = array(
				'SudoRole' => array(
					'name' => $this->request->data['SudoRole']['name'],
					'organization_id' => $this->Auth->user('organization_id')
			));

			$this->SudoRole->id = $id;
			if($this->SudoRole->save($sudoRole)){

				$message = "Saved role " . $sudoRole['SudoRole']['name'];
				
			}
			else {
				$isError = true;
				$message = $this->SudoRole->validationErrorsAsString();
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

			$this->Session->setFlash(__('Any changes to this sudo role will affect ...'),'default',array(),'error');

			$runas = implode(",",$runas);
			$commands = implode(",",$commands);

			$this->set(array(
				'sudoRole' => $sudoRole,
				'runas' => $runas,
				'commands' => $commands
			));
		}
	}
}
