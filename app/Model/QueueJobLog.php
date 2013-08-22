<?php

class QueueJobLog extends AppModel
{
	public $useTable = 'queued_job_log';

    public $belongsTo = array(
        'QueueJob' => array(
            'foreignKey' => 'job_id'
        )
    );
}
