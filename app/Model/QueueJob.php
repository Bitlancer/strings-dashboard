<?php

class QueueJob extends AppModel
{

	public $useTable = 'queued_job';

	public $actsAs = array(
		'OrganizationOwned'
	);

	public $belongsTo = array(
        'Organization'
    );

	public $hasMany = array(
		'QueueJobLog' => array(
            'foreignKey' => 'job_id',
            'dependent' => true
        )
	);

	public $validate = array(
        'http_method' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
            'validMethod' => array(
                'rule' => array('inList',array('get','post','put','delete')),
                'message' => '%%f is an invalid HTTP method'
            )
        ),
		'url' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
            /* Doesn't work when a port is specified?
            'validUrl' => array(
                'rule' => 'url',
                'message' => '%%f is not a valid Url'
            )
            */
		),
		'timeout_secs' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
			'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%%f must be an integer'
            ),
		),
		'remaining_retries' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%%f must be an integer'
            ),
		),
		'retry_delay_secs' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%%f cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%%f must be an integer'
            ),
		),
	);

    public function addJob($url,$body="",$httpMethod='post',$timeoutSecs=90,$retries=40,$retryDelaySecs=30){

        $job = array(
            'QueueJob' => array(
                'url' => $url,
                'body' => $body,
                'http_method' => strtolower($httpMethod),
                'timeout_secs' => $timeoutSecs,
                'remaining_retries' => $retries,
                'retry_delay_secs' => $retryDelaySecs
            )
        );

        $this->create();
        if($this->save($job))
            return $this->id;
        else
            return false;
    }
}
