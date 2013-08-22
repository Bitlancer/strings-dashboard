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
            throw new NotFoundException('Application does not exist.');

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
}
