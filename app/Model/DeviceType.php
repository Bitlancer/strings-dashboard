<?php

class DeviceType extends AppModel
{
    public $useTable = 'device_type';

    public $hasMany = array(
        'Device'
    );
}
