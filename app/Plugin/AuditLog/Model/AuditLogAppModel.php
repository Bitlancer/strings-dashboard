<?php

class AuditLogAppModel extends AppModel {

    public $actsAs = array(
        'Containable',
        'OrganizationOwned'
    );

    /**
     * Prevent a recursive save - found out the hard way ;)
     */
    public function beforeSave($options){
        $this->Behaviors->disable('Auditable');

        if(isset($this->data[$this->name]['created']))
            unset($this->data[$this->name]['created']);

        if(isset($this->data[$this->name]['updated']))
            unset($this->data[$this->name]['updated']);

        return true;
    }
}
