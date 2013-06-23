<?php

class BlueprintsController extends AppController
{

    /**
     * Authorization logic
     */
    public function isAuthorized($user){

        if(parent::isAuthorized($user))
            return true;

        return false;
    }

    public function summary($id=null){

        $blueprint = $this->Blueprint->find('first',array(
            'contain' => array(
            ),
            'fields' => array(
                'id','name','short_description','description',
            ),
            'conditions' => array(
                'Blueprint.id' => $id,
            ),
        ));

        $this->set(array(
            'blueprint' => $blueprint
        ));
    }
}
