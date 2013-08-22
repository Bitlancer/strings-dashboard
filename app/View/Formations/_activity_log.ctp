<?php
  echo $this->element('ActivityLogs/default',array(
    'models' => array(
      'Formation','Device','DeviceDns','Script'
    )
  ));
