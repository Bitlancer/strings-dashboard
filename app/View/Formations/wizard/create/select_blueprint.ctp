<?php
$this->extend('/Formations/wizard/create/_template');

$this->assign('stepTitle','Select a Blueprint');
?>

<div id="select-blueprint">
  <input type="hidden" id="blueprint" name="data[Blueprint][id]" value="" />
  <?php
      echo $this->element('Datatables/basic',array(
        'tableId' => 'blueprint',
        'columnHeadings' => $this->DataTables->getColumnHeadings(),
    ));
  ?>
  <div id="blueprint-summary">
    <a id="back">
      <span class="arrow left"></span>
      Back to Blueprint list
    </a>
    <div id="content">
      <img class="loading" src="/img/loading.gif" />
    </div>
  </div>
</div> <!-- /#select-blueprint -->

<?php
//Script
$this->start('stepScript'); ?>
$('#back').live('click', function() {
    $('#blueprint-summary').css('display','none');
    $('.dataTables_wrapper').show("slide", { direction: "right"});
    $('.cta.submit').removeClass('submit').addClass('disabled');
});

$('.select').live('click', function() {

    var dataSrc = $(this).attr('data-src');

    $(this).closest('form').find('input#blueprint').val($(this).attr('data-id'));

    $('.dataTables_wrapper').css('display','none');
    $('#blueprint-summary').toggle("slide");
    $('.cta.disabled').removeClass('disabled').addClass('submit');

    $.ajax({
      type: "post",
      url: dataSrc,
      dataType: "html",
      success: function(response) {
        $('#blueprint-summary #content').html(response);
      }
    })
    .error(function(jqXHR,textStatus){
      console.log(textStatus);
    });
});
<?php $this->end(); ?>
