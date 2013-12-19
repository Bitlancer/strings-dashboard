<?php
if(empty($auditRecords)){ ?>
  <li>No recent activities</li> 
<?php }
else {
  foreach($auditRecords as $record){ ?>
    <li>
      <?php echo "{$record['user']} {$record['action']} {$record['model']} <strong>{$record['modelName']}</strong>"; ?>
      <small>
        <?php
        echo $this->Time->timeAgoInWords($record['when'],array('timezone' => 'UTC','format' => 'F jS'));
        ?>
      </small>
    </li>  
  <?php }
}
