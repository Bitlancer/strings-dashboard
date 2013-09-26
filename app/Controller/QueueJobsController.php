<?php

class QueueJobsController extends AppController {

    /**
     * Authorization logic
     */
    public function isAuthorized($user){

        if(parent::isAuthorized($user))
            return true;

        switch($this->action){
            case 'liveStatus':
                return true;
        }

        return false;
    }

    /*
    public function liveStatus($jobId=null){

        $job = $this->QueueJob->find('first',array(
            'contain' => array(
                'QueueJobLog' => array(
                    'limit' => 1,
                    'order' => 'created_at DESC'
                )
            ),
            'conditions' => array(
                'QueueJob.id' => $jobId
            )
        ));

        if(empty($job))
            throw new NotFoundException('Job does not exist.');

        $this->set(array(
            'job' => $job
        ));
    }
    */
}
