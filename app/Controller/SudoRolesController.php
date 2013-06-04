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

    public function view($id=null){

        $sudoRole = $this->SudoRole->find('first',array(
            'conditions' => array(
                'SudoRole.id' => $id
            )
        ));

        if(empty($sudoRole)){
            $this->Session->setFlash(__('This sudo role does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        $sudoAttributes = $this->SudoRole->SudoAttribute->find('all',array(
            'link' => array(
                'SudoRole'
            ),
            'fields' => array(
                'SudoAttribute.name','SudoAttribute.value'
            ),
            'conditions' => array(
                'SudoRole.id' => $id,
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

        $this->set(array(
            'sudoRole' => $sudoRole,
            'runas' => $runas,
            'commands' => $commands,
            'isAdmin' => $this->Auth->User('is_admin')
        ));
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
                    'name' => 'sudoRunAs'
                );
				$runasUsers = $this->parseCsvStringToSet($this->request->data['runas']);
                foreach($runasUsers as $user){
                	$sudoAttribute['value'] = $user;
					$sudoAttributes[] = $sudoAttribute;
				}

				//Process commands
                $sudoAttribute = array(
                    'name' => 'sudoCommand'
                );
				$commands = $this->parseCsvStringToSet($this->request->data['commands']);
                foreach($commands as $command){
                    $sudoAttribute['value'] = $command;
					$sudoAttributes[] = $sudoAttribute;
                }

				$sudoRole = array(
					'SudoRole' => array(
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
                    'redirectUri' => '/SudoRoles/view/' . $this->SudoRole->id
                );
            }

            echo json_encode($response);
        }
    }

	public function edit($id=null){

		$sudoRole = $this->SudoRole->find('first',array(
			'conditions' => array(
				'SudoRole.id' => $id,
			)
		));
	
		if(empty($sudoRole)){
			$this->Session->setFlash(__('This sudo role does not exist.'),'default',array(),'error');
			$this->redirect(array('action' => 'index'));
		}

		if($this->request->is('post')){

			$this->autoRender = false;

			$isError = false;
			$message = "";

			$sudoRole = array(
				'SudoRole' => array(
					'name' => $this->request->data['SudoRole']['name'],
			));

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

				$this->SudoRole->id = $id;
				if($this->SudoRole->save($sudoRole)){

					//Diff the list of commands
					$newCommands = $this->parseCsvStringToSet($this->request->data['commands']);
					$newRunAs = $this->parseCsvStringToSet($this->request->data['runas']);

					list($isError,$message) = $this->updateAttributes($id,'sudoCommand',$newCommands);
					if(!$isError){
						list($isError,$message) = $this->updateAttributes($id,'sudoRunAs',$newRunAs);
						if(!$isError){
							$message = "Saved role " . $sudoRole['SudoRole']['name'];
						}
					}
				}
				else {
					$isError = true;
					$message = $this->SudoRole->validationErrorsAsString();
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

			$this->Session->setFlash(__('Any changes to this sudo role will affect ...'),'default',array(),'error');

			$sudoAttributes = $this->SudoRole->SudoAttribute->find('all',array(
            	'link' => array(
                	'SudoRole'
            	),
            	'fields' => array(
                	'SudoAttribute.name','SudoAttribute.value'
            	),
            	'conditions' => array(
                	'SudoRole.id' => $id,
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
			$runas = implode(", ",$runas);
			$commands = implode(",\n",$commands);

			$this->set(array(
				'sudoRole' => $sudoRole,
				'runas' => $runas,
				'commands' => $commands
			));
		}
	}

	public function delete($id){

		$sudoRole = $this->SudoRole->find('first',array(
            'conditions' => array(
                'SudoRole.id' => $id,
            )
        ));
    
        if(empty($sudoRole)){
            $this->Session->setFlash(__('This sudo role does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

		if($this->request->is('post')){

            $this->SudoRole->id = $id;
            if($this->SudoRole->delete($id,true)){
                $this->Session->setFlash(__($sudoRole['SudoRole']['name'] . ' has been deleted.'),'default',array(),'success');
                $this->redirect(array('action' => 'index'));
            }
            else {
                $message = $this->SudoRole->validationErrorsAsString();
                $this->Session->setFlash(__($message), 'default', array(), 'error');
                $this->redirect(array('action' => 'index'));
            }
        }

		$this->set(array(
			'sudoRole' => $sudoRole
		));
	}

	private function updateAttributes($id,$attributeName,$attributes){

		$sudoAttributes = $this->SudoRole->SudoAttribute->find('all',array(
            'link' => array(
                'SudoRole'
            ),
            'fields' => array(
                'SudoAttribute.name','SudoAttribute.value'
            ),
            'conditions' => array(
                'SudoRole.id' => $id,
				'SudoAttribute.name' => $attributeName
            )
        ));

		$existingAttrs = array();
		foreach($sudoAttributes as $attr)
			$existingAttrs[] = $attr['SudoAttribute']['value'];

		//Diff
        $addAttrs = array_diff($attributes,$existingAttrs);
        $deleteAttrs = array_diff($existingAttrs,$attributes);

		if(count($deleteAttrs)){

			$deleteResult = $this->SudoRole->SudoAttribute->deleteAll(array(
        		'SudoAttribute.sudo_id' => $id,
            	'SudoAttribute.name' => $attributeName,
            	'SudoAttribute.value' => $deleteAttrs
        	));

			if(!$deleteResult)
				return array(true,$this->SudoRole->SudoAttribute->validationErrorsAsString());
		}

		if(count($addAttrs)){

        	$sudoAttribute = array(
            	'SudoAttribute' => array(
                    'sudo_id' => $id,
                    'name' => $attributeName
            ));

            $sudoAttributes = array();
            foreach($addAttrs as $attr){
            	$sudoAttribute['SudoAttribute']['value'] = $attr;
                $sudoAttributes[] = $sudoAttribute;
            }

            if(!$this->SudoRole->SudoAttribute->saveMany($sudoAttributes)){
				return array(true,$this->SudoRole->SudoAttribute->validationErrorsAsString(true));
            }
		}

		return array(false,"");
	}

	/**
	 * Parse a comma delimited list into an array of unique non-blank elements
	 */
	private static function parseCsvStringToSet($csvString){

		$elements = explode(',',$csvString);

		$newElements = array();
		foreach($elements as $element){
			$element = trim($element);
			if($element !== '')
				$newElements[] = $element;
		}
		$elements = $newElements;

		$elements = array_unique($elements);
		return $elements;
	}
}
