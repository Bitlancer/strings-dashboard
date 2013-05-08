<?php

class QueueJob extends AppModel
{

	public $useTable = 'queued_job';

	public $belongsTo = array(
        'Organization'
    );

	public $hasMany = array(
		'QueueJobLog'
	);

	public $validate = array(
        'organization_id' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%f cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%f must be an integer'
            ),
            'validForeignKey' => array(
                'rule' => array('isValidForeignKey'),
                'message' => '%f does not exist'
            )
        ),
        'http_method' => array(
            'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%f cannot be empty'
            ),
            'validMethod' => array(
                'rule' => array('inList',array('get','post','put','delete')),
                'message' => '%f is an invalid HTTP method'
            )
        ),
		'url' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%f cannot be empty'
            ),
            'validUrl' => array(
                'rule' => 'url',
                'message' => '%f is not a valid Url'
            )
		),
		'timeout_secs' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%f cannot be empty'
            ),
			'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%f must be an integer'
            ),
		),
		'remaining_retries' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%f cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%f must be an integer'
            ),
		),
		'retry_delay_secs' => array(
			'requiredOnCreate' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'required' => true,
                'message' => '%f is required'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => '%f cannot be empty'
            ),
            'isNumeric' => array(
                'rule' => 'numeric',
                'message' => '%f must be an integer'
            ),
		),
	);
}
