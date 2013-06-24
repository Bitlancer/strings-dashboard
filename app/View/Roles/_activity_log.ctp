<?php
  echo $this->element('ActivityLogs/default',array(
    'models' => array(
      'Role','Profile','Component'
    )
  ));
