<?php

class ApplicationsController extends AppController
{

	/**
     * Home screen containing list of applications and create application CTA
     */
    public function index() {

        $applicationTableColumns = array(
            'Name' => array(
                'model' => 'Application',
                'column' => 'name'
            )
        );

        if($this->request->isAjax()){

            //Datatables
            $findParameters = array(
                'fields' => array(
                    'Application.id','Application.name'
                )
            );

            $dataTable = $this->DataTables->getDataTable($applicationTableColumns,$findParameters);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'applicationTableColumns' => array_keys($applicationTableColumns),
            ));
        }
    }

	public function view($id=null){

        $app = $this->Application->find('first',array(
			'contain' => array(
                'ApplicationFormation' => array(
				    'Formation' => array( 
					    'fields' => array('id','name')
				    )
                ),
                'TeamApplication' => array(
                    'Team' => array(
                        'conditions' => array(
                            'Team.is_disabled' => 0
                        )
                    ),
                    'TeamApplicationSudo' => array(
                        'SudoRole' => array(
                            'fields' => array('id','name')
                        )
                    )
                )
			),
            'conditions' => array(
                'Application.id' => $id,
            )
        ));

        if(empty($app)){
            $this->Session->setFlash(__('This application does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        $formations = array();
        foreach($app['ApplicationFormation'] as $appForm){
            $formations[] = array('Formation' => $appForm['Formation']);
        }

        $permissions = array();
        foreach($app['TeamApplication'] as $teamApp){
            $sudoRoles = array();
            $team = array('Team' => $teamApp['Team']);
            foreach($teamApp['TeamApplicationSudo'] as $teamAppSudo){
                $sudoRoles[] = array('SudoRole' => $teamAppSudo['SudoRole']);
            }
            $permissions[] = array_merge($team,array('SudoRole' => $sudoRoles));
        }

        $this->set(array(
            'isAdmin' => $this->Auth->User('is_admin'),
            'application' => $app,
            'formations' => $formations,
            'permissions' => $permissions,
        ));
    }

	public function create(){

		if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            if($this->Application->save($this->request->data,true,array('name'))){
            	$message = 'Created new application ' . $this->request->data['Application']['name'] . '.';
            }
            else {
                $isError = true;
                $message = $this->Application->validationErrorsAsString();
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
                    'redirectUri' => '/Applications/view/' . $this->Application->id
                );
            }

            echo json_encode($response);
        }
	}

	/**
     * Edit an application
     */
    public function edit($id=null){

        $app = $this->Application->find('first',array(
            'conditions' => array(
                'Application.id' => $id,
            )
        ));

        if(empty($app)){
            $this->Session->setFlash(__('This application does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        if($this->request->is('post')){

            $this->autoRender = false;

            $isError = false;
            $message = "";

            $validFields = array('name');
            $this->Application->id = $id;
            if($this->Application->save($this->request->data,true,$validFields)){
                $message = 'Updated application ' . $app['Application']['name'] . '.';
            }
            else {
                $isError = true;
                $message = $this->Application->validationErrorsAsString();
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
                'application' => $app
            ));
        }
    }

	public function delete($id=null){

        $app = $this->Application->find('first',array(
            'conditions' => array(
                'Application.id' => $id,
            )
        ));

        if(empty($app)){
            $this->Session->setFlash(__('This application does not exist.'),'default',array(),'error');
            $this->redirect(array('action' => 'index'));
        }

        if($this->request->is('post')){

            $this->Application->id = $id;
            if($this->Application->delete()){
                $this->Session->setFlash(__($app['Application']['name'] . ' has been deleted.'),'default',array(),'success');
                $this->redirect(array('action' => 'index'));
            }
            else {
                $message = $this->Application->validationErrorsAsString();
                $this->Session->setFlash(__($message), 'default', array(), 'error');
                $this->redirect(array('action' => 'index'));
            }
        }

        $this->set(array(
            'application' => $app
        ));
    }

	public function editFormations($id=null){

        $application = $this->Application->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Application.id' => $id
            )
        ));

        if(empty($application)){
            $this->setFlash('Application does not exist');
            $this->redirect(array('action' => 'index'));
        }

        $formationTableColumns = array(
            'Name' => array(
                'model' => 'Formation',
                'column' => 'name'
            )
        );

        if($this->isJsonRequest()){

            $findParameters = array(
                'contain' => array(
                    'Formation'
                ),
                'conditions' => array(
                    'ApplicationFormation.application_id' => $id
                )
            );

            $dataTable = $this->DataTables->getDataTable($formationTableColumns,$findParameters,$this->Application->ApplicationFormation);

            $this->set(array(
                'dataTable' => $dataTable,
                'isAdmin' => $this->Auth->User('is_admin')
            ));
        }
        else {
            $this->set(array(
                'application' => $application
            ));
        }
	}

	public function addFormation($id=null){

		$this->autoRender = false;

		$isError = false;
		$message = "";
		$memberId = 0;

		$formationName = "";
        if($this->request->is('post'))
            $formationName = $this->request->data['name'];
        else
            $formationName = $this->request->query['name'];

        $this->loadModel('Formation');

		$formation = $this->Formation->find('first',array(
			'fields' => array(
				'Formation.id','Formation.name'
			),
			'conditions' => array(
				'Formation.name' => $formationName,
			)
		));

		if(empty($formation)){
			$isError = true;
			$message = 'No such formation found';
		}
		else {
			$formationId = $formation['Formation']['id'];

			//Check if this formation is already a member of this application
			$count = $this->Application->ApplicationFormation->find('count',array(
				'conditions' => array(
					'ApplicationFormation.application_id' => $id,
					'ApplicationFormation.formation_id' => $formationId
				)
			));
			if(!$count){
                $appFormation = array(
                    'ApplicationFormation' => array(
                        'application_id' => $id,
                        'formation_id' => $formationId
                    )
                );
				if($this->Application->ApplicationFormation->save($appFormation))
					$memberId = $formationId;
				else {
					$isError = true;
					$message = 'Unable to add this formation to this application';
                    $message = $this->Application->ApplicationFormation->validationErrorsAsString();
				}
			}
		}

		echo json_encode(array(
    		'isError' => $isError,
			'message' => __($message),
    		'id' => $memberId
		));
	}

	public function removeFormation($applicationId=null){

		$this->autoRender = false;

		$formationId = 0;
        if($this->request->is('post'))
            $formationId = $this->request->data['id'];
        else
            $formationId = $this->request->query['id'];	

		$this->Application->ApplicationFormation->deleteAll(
            array(
                'ApplicationFormation.application_id' => $applicationId,
                'ApplicationFormation.formation_id' => $formationId
            )
        );
	}

	public function editPermissions($id=null){

        $this->loadModel('Team');

        $application = $this->Application->find('first',array(
            'conditions' => array(
                'Application.id' => $id
            )
        ));

        if(empty($application)){
            $this->setFlash('Application does not exist.');
            $this->redirect(array('action' => 'index'));
        }

        $teams = $this->Team->find('all',array(
            'contain' => array(),
            'conditions' => array(
                'Team.is_disabled' => 0,
            ),
        ));

        //Get first teams associations
        $firstTeam = $teams[0];
        $firstTeamApp = $this->Application->TeamApplication->find('first',array(
            'contain' => array(
                'TeamApplicationSudo'
            ),
            'conditions' => array(
                'TeamApplication.application_id' => $id,
                'TeamApplication.team_id' => $firstTeam['Team']['id']
            )
        ));
        $firstTeam = array_merge($firstTeam,$firstTeamApp);

        $this->set(array(
            'application' => $application,
            'teams' => $teams,
            'firstTeam' => $firstTeam
        ));
        
	}

    public function editTeamPermissions($applicationId=null,$teamId=null){

        $this->loadModel('TeamApplicationSudo');

        $this->autoRender = false;

        $isError = false;
        $message = "";

        $grantLogin = false;
        $grantSudo = false;

        $teamApp = $this->Application->TeamApplication->find('first',array(
            'contain' => array(
                'TeamApplicationSudo'
            ),
            'conditions' => array(
                'TeamApplication.application_id' => $applicationId,
                'TeamApplication.team_id' => $teamId
            )
        ));

        if($this->request->is('post')){

            $grantLogin = $this->request->data['grantLogin'] == 'true';
            $grantSudo = $this->request->data['grantSudo'] == 'true';
            if(!$grantLogin)
                $grantSudo = false;

            if($grantLogin){

                //Create application team associations
                if(empty($teamApp)){

                    $newTeamApp = array(
                        'TeamApplication' => array(
                            'application_id' => $applicationId,
                            'team_id' => $teamId
                        )
                    );
                    if(!$this->Application->TeamApplication->save($newTeamApp)){
                        $isError = true;
                        $message = "We encountered an error while granting this team login permissions.";
                        $message = $this->Application->TeamApplication->validationErrorsAsString();
                    }
                }

                //Destroy application team sudo associations
                if(!empty($teamApp) && !$grantSudo){
                    $result = $this->TeamApplicationSudo->deleteAll(
                        array(
                            'TeamApplicationSudo.team_application_id' => $teamApp['TeamApplication']['id']
                        )
                    );
                    if(!$result){
                        $isError = true;
                        $message = "We encountered an error while removing this team's privileges.";
                    }
                }
            }
            else {
                //Delete all assoications
                if(!empty($teamApp)){
                    if(!$this->Application->TeamApplication->delete($teamApp['TeamApplication']['id'])){
                        $isError = true;
                        $message = "We encountered an error while removing this team's privileges.";
                    }
                }
            }

            //If error encountered reset flags to original values
            if($isError){
                $grantLogin = !$grantLogin;
                $grantSudo = !$grantSudo;
            }
        }
        else {
            if(!empty($teamApp)){
                $grantLogin = true;
                if(count($teamApp['TeamApplicationSudo']))
                    $grantSudo = true;
            }
        }

        echo json_encode(array(
            'isError' => $isError,
            'message' => __($message),
            'grantLogin' => (bool) $grantLogin,
            'grantSudo' => (bool) $grantSudo
        ));
    }

    public function teamSudoRoles($modelId=null,$teamId=null){

        $associationModel = 'TeamApplication';

        $model = $this->modelClass;
        $sudoAssociationModel = "{$associationModel}Sudo";
        $sudoAssociationFK = $this->$model->$associationModel->hasMany[$sudoAssociationModel]['foreignKey'];

        $this->loadModel($sudoAssociationModel);

        $teamAssoc = $this->$model->$associationModel->find('first',array(
            'contain' => array(),
            'conditions' => array(
                "$associationModel.application_id" => $modelId,
                "$associationModel.team_id" => $teamId,
            ),
        ));

        $teamAssocId = empty($teamAssoc) ? 0 : $teamAssoc[$associationModel]['id'];

        $teamAssocSudoTableColumns = array(
            'Name' => array(
                'model' => 'SudoRole',
                'column' => 'name'
            )
        );

        $findParameters = array(
            'contain' => array(
                'SudoRole'
            ),
            'conditions' => array(
                "$sudoAssociationModel.$sudoAssociationFK" => $teamAssocId
            )
        );

        $dataTable = $this->DataTables->getDataTable($teamAssocSudoTableColumns,$findParameters,$this->$sudoAssociationModel);

        $this->set(array(
            'dataTable' => $dataTable,
            'isAdmin' => $this->Auth->User('is_admin')
        ));
    }

    public function addSudoRoleToTeam($modelId=null,$teamId=null){

        $this->autoRender = false;

        $model = $this->modelClass;
        $associationModel = 'TeamApplication';
        $sudoAssociationModel = 'TeamApplicationSudo';

        $this->loadModel('SudoRole');
        $this->loadModel($sudoAssociationModel);

        //Get association model foreign key
        $associationModelFK = 'application_id';
        $sudoAssociationModelFK = 'team_application_id';

        $isError = false;
        $message = "";
        $newSudoAssocId = 0;

        //Get the association model id
        $teamAssoc = $this->$model->$associationModel->find('first',array(
            'contain' => array(),
            'conditions' => array(
                "$associationModel.$associationModelFK" => $modelId,
                "$associationModel.team_id" => $teamId
            )
        ));

        if(empty($teamAssoc)){
            $isError = true;
            $message = "This team is not associated with this $model.";
        }
        else {

            //Get sudo role by name
            $sudoRole = $this->SudoRole->findByName($this->request->data['name']);
            if(empty($sudoRole)){
                $isError = true;
                $message = "This sudo role does not exist.";
            }
            else {

                $sudoRoleId = $sudoRole['SudoRole']['id'];

                //Check if this sudo role is already associated with this team
                $existingAssoc = $this->$sudoAssociationModel->findBySudoId($sudoRoleId);
                if(empty($existingAssoc)){

                    $teamAssocSudo = array(
                        "$sudoAssociationModel" => array(
                            "$sudoAssociationModelFK" => $teamAssoc[$associationModel]['id'],
                            'sudo_id' => $sudoRole['SudoRole']['id']
                    ));
                            
                    if($this->$sudoAssociationModel->save($teamAssocSudo)){
                        $newSudoAssocId = $this->$sudoAssociationModel->id;
                    }
                    else {
                        $isError = true;
                        $message = "Failed to associate this sudo role with this team.";
                    }
                }
            }
        }

        echo json_encode(array(
            'isError' => $isError,
            'message' => __($message)
        ));
    }

    public function removeSudoRoleFromTeam($modelId=null,$teamId=null){

        $this->autoRender = false;

        $model = $this->modelClass;
        $associationModel = 'TeamApplication';
        $sudoAssociationModel = 'TeamApplicationSudo';

        $this->loadModel('SudoRole');
        $this->loadModel($sudoAssociationModel);

        //Get association model foreign key
        $associationModelFK = 'application_id';
        $sudoAssociationModelFK = 'team_application_id';

        $isError = false;
        $message = "";

        //Get the association model id
        $teamAssoc = $this->$model->$associationModel->find('first',array(
            'contain' => array(),
            'conditions' => array(
                "$associationModel.$associationModelFK" => $modelId,
                "$associationModel.team_id" => $teamId
            )
        ));

        if(empty($teamAssoc)){
            $isError = true;
            $message = "This team is not associated with this $model.";
        }
        else {
            $result = $this->$sudoAssociationModel->deleteAll(
                array(
                    "sudo_id" => $this->request->data['id']
                )
            );

            if(!$result){
                $isError = true;
                $message = "Failed to remove the association between this sudo role and this team.";
            }
        }

        echo json_encode(array(
            'isError' => $isError,
            'message' => __($message)
        )); 
    }

    public function deploy($id=null){

    }

    public function dns($id=null){

    }
}
