<?php
  echo $this->element('ActivityLogs/default',array(
    'models' => array(
      'Application','Formation','Device'
    )
  ));
