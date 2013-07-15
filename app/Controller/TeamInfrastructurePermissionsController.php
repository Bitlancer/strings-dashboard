<?php

class TeamInfrastructurePermissionsController extends AppController {

    public $uses = false;

    /*
     * The models specified below are the only which a user may edit
     * edit infrastructure permissions for
     */
    protected $validModels = array('Application','Formation','Role','Device');

    /**
     * Authorization logic
     */
    public function isAuthorized($user){

        if(parent::isAuthorized($user))
            return true;

        return false;
    }

    public function edit($model=null,$id=null){

        if(!in_array($model,$this->validModels))
            throw new BadRequestException('Invalid model specified.');

        //Load models
        $this->loadModel($model);
        $this->loadModel('Team');

        $entity = $this->$model->find('first',array(
            'contain' => array(),
            'conditions' => array(
                "$model.id" => $id
            )
        ));

        if(empty($entity)){
            $this->setFlash("This $model does not exist.");
            $this->redirect(array('action' => 'index'));
        }

        $teams = $this->Team->find('all',array(
            'contain' => array(),
            'conditions' => array(
                'Team.is_disabled' => 0,
            ),
        ));

        $this->set(array(
            'model' => $model,
            'entityId' => $id,
            'teams' => $teams,
        ));
    }

    public function editTeamPermissions($model=null,$modelId=null,$teamId=null){

        if(!in_array($model,$this->validModels))
            throw new BadRequestException('Invalid model specified.');

        $this->autoRender = false;

        //Models
        $teamModel = "Team$model";
        $teamModelSudo = "{$teamModel}Sudo";

        //Load model
        $this->loadModel($model);
        $this->loadModel($teamModelSudo);

        //Model foreign keys
        $teamModelFK = $this->$model->hasMany[$teamModel]['foreignKey'];
        $teamModelSudoFK = $this->$model->$teamModel->hasMany[$teamModelSudo]['foreignKey'];

        $isError = false;
        $message = "";

        $grantLogin = false;
        $grantSudo = false;

        $teamModelAssocObject = $this->$model->$teamModel->find('first',array(
            'contain' => array(
                "$teamModelSudo"
            ),
            'conditions' => array(
                "$teamModel.$teamModelFK" => $modelId,
                "$teamModel.team_id" => $teamId
            )
        ));

        if($this->request->is('post')){

            $grantLogin = $this->request->data['grantLogin'] == 'true';
            $grantSudo = $this->request->data['grantSudo'] == 'true';

            if(!$grantLogin)
                $grantSudo = false;

            if($grantLogin){

                //Create association between this model and the team
                if(empty($teamModelAssocObject)){

                    $newTeamModelObject = array(
                        "$teamModel" => array(
                            "$teamModelFK" => $modelId,
                            'team_id' => $teamId
                        )
                    );
                    if(!$this->$model->$teamModel->save($newTeamModelObject)){
                        $isError = true;
                        $message = "We encountered an error while granting this team login permissions.";
                    }
                }

                //Destroy application team sudo associations
                if(!empty($teamModelAssocObject) && !$grantSudo){
                    $result = $this->$teamModelSudo->deleteAll(
                        array(
                            "$teamModelSudo.$teamModelSudoFK" => $teamModelAssocObject[$teamModel]['id']
                        )
                    );
                    if(!$result){
                        $isError = true;
                        $message = "We encountered an error while removing this team's privileges.";
                    }
                }
            }
            else {
                //Delete all associations
                if(!empty($teamModelAssocObject)){
                    if(!$this->$model->$teamModel->delete($teamModelAssocObject[$teamModel]['id'])){
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
            if(!empty($teamModelAssocObject)){
                $grantLogin = true;
                if(count($teamModelAssocObject[$teamModelSudo]))
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

    public function teamSudoRoles($model=null,$modelId=null,$teamId=null){

        if(!in_array($model,$this->validModels))
            throw new BadRequestException('Invalid model specified.');

        //Models
        $teamModel = "Team$model";
        $teamModelSudo = "{$teamModel}Sudo";

        //Load
        $this->loadModel($model);
        $this->loadModel($teamModelSudo); 

        //Foreign keys
        $teamModelFK = $this->$model->hasMany[$teamModel]['foreignKey'];
        $teamModelSudoFK = $this->$model->$teamModel->hasMany[$teamModelSudo]['foreignKey'];

        $teamModelObject = $this->$model->$teamModel->find('first',array(
            'contain' => array(),
            'conditions' => array(
                "$teamModel.$teamModelFK" => $modelId,
                "$teamModel.team_id" => $teamId,
            ),
        ));

        $modelTeamId = empty($teamModelObject) ? 0 : $teamModelObject[$teamModel]['id'];

        $this->DataTables->setColumns(array(
            'Name' => array(
                'model' => 'SudoRole',
                'column' => 'name'
            )
        ));

        $this->DataTables->process(
            array(
                'contain' => array(
                    'SudoRole'
                ),
                'conditions' => array(
                    "$teamModelSudo.$teamModelSudoFK" => $modelTeamId
                )
            ),
            $this->$teamModelSudo
        );

        $this->set(array(
            'isAdmin' => $this->Auth->User('is_admin')
        ));
    }
    
    public function addSudoRoleToTeam($model=null,$modelId=null,$teamId=null){

        if(!in_array($model,$this->validModels))
            throw new BadRequestException('Invalid model specified.');

        $this->autoRender = false;
       
        $teamModel = "Team$model";
        $teamModelSudo = "{$teamModel}Sudo";

        $this->loadModel($model);
        $this->loadModel('SudoRole');
        $this->loadModel($teamModelSudo);

        //Get association model foreign key
        $teamModelFK = $this->$model->hasMany[$teamModel]['foreignKey'];
        $teamModelSudoFK = $this->$model->$teamModel->hasMany[$teamModelSudo]['foreignKey'];

        $isError = false;
        $message = "";

        //Get the association model id
        $teamModelObject = $this->$model->$teamModel->find('first',array(
            'contain' => array(),
            'conditions' => array(
                "$teamModel.$teamModelFK" => $modelId,
                "$teamModel.team_id" => $teamId
            )
        ));

        if(empty($teamModelObject)){
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
                $existingTeamModelSudoObject = $this->$teamModelSudo->find('first',array(
                    'contain' => array(),
                    'conditions' => array(
                        "$teamModelSudoFK" => $teamModelObject[$teamModel]['id'],
                        'sudo_id' => $sudoRoleId
                    )
                ));
                if(empty($existingTeamModelSudoObject)){

                    $newTeamModelSudoObject = array(
                        "$teamModelSudo" => array(
                            "$teamModelSudoFK" => $teamModelObject[$teamModel]['id'],
                            'sudo_id' => $sudoRoleId
                    ));

                    if(!$this->$teamModelSudo->save($newTeamModelSudoObject)){
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

    public function removeSudoRoleFromTeam($model=null,$modelId=null,$teamId=null){

        if(!in_array($model,$this->validModels))
            throw new BadRequestException('Invalid model specified.');

        $this->autoRender = false;

        $teamModel = "Team$model";
        $teamModelSudo = "{$teamModel}Sudo";

        $this->loadModel($model);
        $this->loadModel('SudoRole');
        $this->loadModel($teamModelSudo);

        //Get association model foreign key
        $teamModelFK = $this->$model->hasMany[$teamModel]['foreignKey'];
        $teamModelSudoFK = $this->$model->$teamModel->hasMany[$teamModelSudo]['foreignKey']; 

        $isError = false;
        $message = "";

        //Get the association model id
        $teamModelObject = $this->$model->$teamModel->find('first',array(
            'contain' => array(),
            'conditions' => array(
                "$teamModel.$teamModelFK" => $modelId,
                "$teamModel.team_id" => $teamId
            )
        ));

        if(empty($teamModelObject)){
            $isError = true;
            $message = "This team is not associated with this $model.";
        }
        else {
            $result = $this->$teamModelSudo->deleteAll(
                array(
                    "$teamModelSudoFK" => $teamModelObject[$teamModel]['id'],
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
}
