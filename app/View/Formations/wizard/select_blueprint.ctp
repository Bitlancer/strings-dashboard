<?php
$this->extend('/Formations/wizard/_formation_wizard');

$this->assign('stepNumber','2');
$this->assign('stepTitle','Select a Blueprint');

//Styling
$this->start('stepStyle'); ?>
  table#blueprint td .blueprint {
    margin-bottom:4px;
  }
  table#blueprint td .blueprint h3 {
    width:88%;
    display:inline;
    font-weight:bold;
  }
  table#blueprint td .blueprint a.action {
    float:right;
    width:10%;
    text-align:right;
  }
  table#blueprint td .blueprint-description {
    margin-right:10%;
    font-size:13px;
  }
  div#blueprint-summary {
    display:none;
  }
  div#blueprint-summary a#back {
    display:block;
    margin-bottom:10px;
  }
  div#blueprint-summary div#content {
    min-height:100px;
    border:1px solid #e3e3e3;
    background-color:#fcfcfc;
  }
  div#blueprint-summary div#content img.loading {
    display:block;
    margin-top:45px;
    margin-left:auto;
    margin-right:auto;
  }
  div#blueprint-summary div#content .title {
    margin:0;
    padding:6px;
    background-color:#f0f0f0;
  }
  div#blueprint-summary div#content .title h2 {
    margin:0;
  }
  div#blueprint-summary div#content .description {
    padding:10px;
  }
  div#blueprint-summary div#content p {
    margin-bottom:10px;
  }
<?php $this->end();

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

<?php //Main content ?>
<input type="hidden" id="blueprint" name="data[Blueprint][id]" value="" />
<?php
    echo $this->element('Datatables/basic',array(
      'tableId' => 'blueprint',
      'columnHeadings' => $blueprintTableColumns,
  ));
?>
<div id="blueprint-summary">
  <a id="back">&#60; Back to Blueprint list</a>
  <div id="content">
    <img class="loading" src="/img/loading.gif" />
  </div>
</div>

<?php
//Sidebar
$this->start('sidebar'); ?>
<h2>Help</h2>
<p>A <strong>blueprint</strong> is a template for spinning up a collection of related devices that should be managed together. Within the application stack, a blueprint sits between the device layer and the application layer. For a full  explanation of String's application model please visit our <a href="#">tutorial</a>.</p>
<?php $this->end(); ?>

