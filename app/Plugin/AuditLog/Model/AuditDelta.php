<?php

class AuditDelta extends AuditLogAppModel {

    public $useTable = 'audit_delta';

    public $belongsTo = array(
        'Organization',
    );
}
