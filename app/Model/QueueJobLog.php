<?php

class QueueJobLog extends AppModel
{
	public $useTable = 'queued_job';

    public $belongsTo = array(
        'QueueJob'
    );
}
