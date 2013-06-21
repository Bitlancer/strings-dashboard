<?php

class AuditsController extends AppController {

    public $uses = array(
        'AuditLog.Audit'
    );

    public function recentActivities(){

        /*
        * List of columns that will be used to identify a model.
        */
        $nameColumns = array(
            'name'
        );

        $parameters = array();
        if($this->request->is('get'))
            $parameters = $this->request->query;
        else
            $parameters = $this->request->data;

        //Limit results
        $limit = isset($parameters['limit']) ? $parameters['limit'] : 15;

        //Filter the results
        $conditions = array();
        if(isset($parameters['models'])){
            $conditions['Audit.model'] = explode(',',$parameters['models']); 
        }
        if(isset($parameters['users'])){
            $conditions['Audit.users'] = explode(',',$parameters['users']);
        }

        $auditRecords = $this->Audit->find('all',array(
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'User.name'
                    )
                )
            ),
            'fields' => array(
                'Audit.event','Audit.model','Audit.json_object','Audit.created'
            ),
            'conditions' => $conditions,
            'limit' => $limit,
            'order' => array('Audit.created DESC')
        ));

        foreach($auditRecords as $index => $record){

            $model = $record['Audit']['model'];

            $summary = array(
                'user' => $record['User']['name'],
                'action' => $record['Audit']['event'] . "d",
                'model' => strtolower(Inflector::humanize(Inflector::underscore($model))),
                'when' => $record['Audit']['created']
            );

            //Determine the "name" of the object that was modified
            $modifiedModel = $record['Audit']['json_object'];
            $modifiedModel = json_decode($modifiedModel,true);
            $modelName = 'Unknown';
            foreach($nameColumns as $column){
                if(isset($modifiedModel[$model][$column]))
                    $modelName = $modifiedModel[$model][$column];
            }
            $summary['modelName'] = strtolower($modelName);

            $auditRecords[$index] = $summary;
        }

        $this->set(array(
            'auditRecords' => $auditRecords
        ));
    }

}
