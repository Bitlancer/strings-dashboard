<?php
$this->extend('/Common/standard');

$this->assign('title', $user['User']['name']);

//Set sidebar content
$this->start('sidebar');
echo $this->element('../Users/_activity_log');
$this->end();
?>

<!-- Main content -->
<section id="user-keys" class="association" data-src-remove="/Users/removeSshKey/<?php echo $user['User']['id']; ?>" >
<?php
  echo $this->element('Datatables/default',array(
    'model' => 'UserKey',
    'title' => 'SSH Keys',
    'tableId' => 'keys',
    'columnHeadings' => $this->DataTables->getColumnHeadings(),
    'processing' => true,
    'ctaModal' => true,
    'ctaSrc' => '/Users/addSshKey/' . $user['User']['id'],
    'ctaButtonText' => 'Add Key',
    'ctaTitle' => 'Add Key',
    'ctaWidth' => 500
  ));
?> 
</section>
