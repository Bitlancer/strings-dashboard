<?php

class ScriptsController extends AppController
{
    /*
     * The models specified below are the only ones which may be associated
     * with a script.
     */
    protected $validModels = array('Application','Formation');

    /**
     * Authorization logic
     */
    public function isAuthorized($user){

        if(parent::isAuthorized($user))
            return true;

        switch($this->action){
            case 'index':
                return true;
        }

        return false;
    }

    public function index($model,$modelId){

        if(!in_array($model,$this->validModels))
            throw new BadRequestException('Invalid model specified.');

        $this->DataTables->setColumns(array(
            'Script' => array(
                'model' => 'Script',
                'column' => 'name'
            )
        ));

        if($this->request->is('ajax')){

            $this->DataTables->process(
                array(
                    'contain' => array(),
                    'fields' => array('Script.*'),
                    'conditions' => array(
                        'Script.Model' => $model,
                        'Script.foreign_key_id' => $modelId
                    )
                ),
                $this->Script
            );
        }
    }

    public function create($model,$modelId){

        $this->loadModel($model);

        if($this->request->is('post')){

            $isError = false;
            $message = null;
            $redirectUri = null;

            if(!in_array($model,$this->validModels)){
                $isError = true;
                $message = 'Invalid model specified.';
            }
            elseif(!$this->$model->exists($modelId)){
                $isError = true;
                $message = "$model does not exist.";
            }
            else {

                $script = array_merge_recursive(
                    $this->request->data,
                    array(
                        'Script' => array(
                            'model' => $model,
                            'foreign_key_id' => $modelId
                        )
                    )
                );

                $validFields = array(
                    'model','foreign_key_id','name','type',
                    'url','path','parameters'
                );

                if(!$this->Script->save($script,true,$validFields)){
                    $isError = true;
                    $message = $this->Script->validationErrorsAsString();
                }
                else
                    $redirectUri = "/Applications/view/$modelId";
            }

            $this->outputAjaxFormResponse($message,$isError,$redirectUri);
        }
        else {
            $this->render('create-edit');
        }
    }

    public function edit($scriptId){

        $script = $this->Script->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Script.id' => $scriptId
            )
        ));

        if(empty($script))
            throw new NotFoundException('Script does not exist.');

        if($this->request->is('post')){

            $isError = false;
            $message = null;
            $redirectUri = null;

            $validFields = array('name','type','url','path','parameters');

            $this->Script->id = $scriptId;
            if($this->Script->save($this->request->data,true,$validFields)){
                $message = 'Script updated successfully.';
                $redirectUri = $this->referer();
            }
            else {
                $isError = true;
                $message = $this->Script->validationErrorsAsString();
            }

            $this->outputAjaxFormResponse($message,$isError,$redirectUri);
        }
        else {
            
            $this->set(array(
                'script' => $script
            ));

            $this->render('create-edit');
        }
    }

    public function delete($scriptId=null){

        $script = $this->Script->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Script.id' => $scriptId,
            )
        ));

        if(empty($script))
            throw new NotFoundException('Script does not exist.');

        if($this->request->is('post')){

            $scriptName = $script['Script']['name'];

            $this->Script->id = $scriptId;
            if($this->Script->delete()){
                $this->setFlash("Script $scriptName has been deleted.",'success');
                $this->redirect($this->referer('/'));
            }
            else {
                $this->setFlash($this->Script->validationErrorsAsString(),'error');
                $this->redirect($this->referer('/'));
            }
        }

        $this->set(array(
            'script' => $script
        ));
    }

    public function run($scriptId=null){

        $this->loadModel('QueueJob');

        $script = $this->Script->find('first',array(
            'contain' => array(),
            'conditions' => array(
                'Script.id' => $scriptId,
            )
        ));

        if(empty($script))
            throw new NotFoundException('Script does not exist.');

        $qJob = array(
            'QueueJob' => array(
                'http_method' => 'post',
                'url' => STRINGS_API_URL . '/RemoteExecution/run/' . $scriptId,
                'timeout_secs' => 60 * 15,
                'remaining_retries' => 1,
                'retry_delay_secs' => 0
            )
        );

        if(!$this->QueueJob->save($qJob)){
            die($this->QueueJob->validationErrorsAsString());
            throw new InternalErrorException('Failed to schedule queue job to run this script');
        }

        $qJobId = $this->QueueJob->id;
        $this->redirect(array('action' => 'status',$qJobId));
    }

    public function status($jobId=null){

        $this->loadModel('QueueJob');

        $job = $this->QueueJob->find('first',array(
            'contain' => array(
                'QueueJobLog' => array(
                    'limit' => 1
                )
            ),
            'conditions' => array(
                'QueueJob.id' => $jobId
            )
        ));

        if(empty($job))
            throw new NotFoundException('Job does not exist.');

        //Determine the status msg and output
        $hasCompleted = false;
        $status = "Checking job status, please wait.";
        $output = "";
        if(empty($job['QueueJob']['last_started_at'])){
            $status = 'Waiting for job to execute.';
        }
        elseif(empty($job['QueueJob']['last_response']))
            $status = 'Executing script on jump server.';
        else {
            $hasCompleted = true;
            $apiResponse = json_decode($job['QueueJob']['last_response'],true);
            $rawOutput = $apiResponse['data'];
            $status = "The script terminated with an exit code of " . $rawOutput['exitCode'];
            $output = implode("\n",$rawOutput['output']);
        }

        if($this->request->is('ajax')){
            $this->autoRender = false;
            echo json_encode(array(
                'hasCompleted' => $hasCompleted,
                'status' => $status,
                'output' => $output
            ));
        }
        else {
            $this->set(array(
                'status' => $status,
                'output' => $output
            ));
        }
    }
}
