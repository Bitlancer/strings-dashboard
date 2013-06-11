<?php

class BlueprintsController extends AppController
{

    public function index(){

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
