<?php

class Audit extends AuditLogAppModel {

    public $useTable = 'audit';

    public $belongsTo = array(
        'Organization',
        'User'
    );
}
