<?php
  echo $this->element('ActivityLogs/default',array(
    'models' => array(
      'User','Team'
    )
  ));
