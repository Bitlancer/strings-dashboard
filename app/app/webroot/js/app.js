var strings = {

  client : {
    init : function() {
      if(!$('header').html().length) strings.client.include();
    },
    loaded : function() {
      if($('body').hasClass('loading')) $('body').removeClass('loading');
      strings.ui.tables();
      strings.events.forms();
      strings.events.clicks();
      strings.ui.actionmenu();
    },
    include : function() {}
  },
  
  ui : {
    messages : function(type,title,message) {
      if(!title) title = type;
      if(!$('ul#messages').length) {
        var container = $('body > section > div');
        if($('section > div.columns').length) container = $('body > section > div.columns > div').first();
        container.prepend('<ul id="messages"></ul>');
      }
      $('body section ul#messages').prepend('<li class="'+type+'"><span>'+title+'</span>'+message+'<a class="close"></a></span>');
    },
    actionmenu : function() {
      $('ul.action-menu').live('click',function(event) {
        event.stopPropagation();
        var align = ($(this).outerWidth() - $(this).children('span').outerWidth()) * 0.5;
        if($(this).attr('data-width')){
          $(this).children('span').width($(this).attr('data-width'));
          align = 0;
        }
        $(this).children('span').css('right',align);
        $(this).toggleClass('active');
      });
      $('body').click(function() {
        $('ul.action-menu.active').removeClass('active');
      });
    },
    modal : function(obj) {
      var modal = $(obj.attr('data-src'));
      var opt = {
        modal: true,
        title: obj.attr('data-title') || 'No Title',
        width: obj.attr('data-width') || '360',
        dialogClass:'strings-modal',
        open: function() {
          console.log(modal);
        }
      };
      modal.dialog(opt).dialog('open');
      modal.find('.cta:not(".cancel,.primary")').bind('click', function() {
        modal.dialog('destroy');
      });
    },
    tables : function() {
      $('table[data-type="datatable"]').not('.example table').each(function() {
        $(this).dataTable({
          "sPaginationType": "full_numbers",
          "aLengthMenu": [[2, 10, 25, 50, 100, 200, -1], [2, 10, 25, 50, 100, 200, "All"]],
          "iDisplayLength": parseInt($(this).attr('data-length')) || 10,
          "oLanguage": { "sSearch": "" },
          "sDom": '<"top"f>rt<"bottom"p><"clear">',
          "fnInitComplete": function(oSettings) {
            var parent = $(this).parents('.dataTables_wrapper');
            if($(this).attr('data-title')) parent.find('.top').prepend('<h2>'+$(this).attr('data-title')+'</h2>');
            parent.find('.dataTables_filter input').attr('placeholder','Search');
          }
        });
      });
      //$('.dataTables_length').remove();
    }
  },
  
  events : {
    clicks : function() {
      // close message boxes
      $('ul#messages li a.close').live('click', function() { $(this).parent().remove() });
      // tooltips
      $('.tooltip').tooltip({ position: { my: "left+2 top+14", at: "left top+14" } });
      // modal windows
      $('.modal').click(function(){strings.ui.modal($(this))});
    },
    forms : function() {
      // count type validation
      $('.cta.disabled').each(function() {
        $(this).parents('form').find(':password').keyup(function() {
          if($(this).val().length > 5){
            $(this).parents('form').find('.disabled').toggleClass('disabled not-disabled');
          } else {
            $(this).parents('form').find('.not-disabled').toggleClass('not-disabled disabled');
          }
        });
      });
      // auto-complete
      $('.autocomplete').not('.example > .autocomplete').each(function() {
        $(this).textext({
          plugins : 'ajax autocomplete prompt tags',
          prompt : $(this).attr('data-prompt'),
          ajax : {
              url : $(this).attr('data-src'),
              dataType : 'json',
              cacheResults : true
          }
        });
      });
    }
  }
  
};

$(document).ready(strings.client.init());

$(window).load(strings.client.loaded());
