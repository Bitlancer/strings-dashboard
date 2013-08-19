var strings = {

  client: {
    init: function() {
      if(!$('header').html().length) strings.client.include();
    },
    loaded: function() {
      if($('body').hasClass('loading')) $('body').removeClass('loading');
      strings.ui.tables.attach('table[data-type="datatable"]');
      strings.associations.attach('.association');
      strings.events.forms();
      strings.events.clicks();
      strings.events.keypress();
      strings.ui.actionmenu();
      strings.ui.recent_activites();
    },
    include: function() {
    }
  },
  
  ui: {
    messages: function(type,title,message) {
      if(!title) title = type;
      if(!$('ul#messages').length) {
        var container = $('body > section > div');
        if($('section > div.columns').length) container = $('body > section > div.columns > div').first();
        container.prepend('<ul id="messages"></ul>');
      }
      $('body section ul#messages').prepend('<li class="'+type+'"><span>'+title+'</span>'+message+'<a class="close"></a></span>');
    },
    actionmenu: function() {
      $('ul.action-menu').live('click',function(event) {
        event.stopPropagation();
        var offset = ($(this).outerWidth() - $(this).children('span').outerWidth()) * 0.5;
        if($(this).attr('data-width')){
          $(this).children('span').width($(this).attr('data-width'));
          offset = 0;
        }
        var align = "right";
        if($(this).attr('data-align')){
          align = $(this).attr('data-align');
        }
        $(this).children('span').css(align,offset);
        $(this).toggleClass('active');
      });
      $('body').click(function() {
        $('ul.action-menu.active').removeClass('active');
      });
    },
    modal: function(obj) {
      var modal;
      var loadFromServer = false;
      if(obj.attr('data-src')[0] == '#') {
        modal = $(obj.attr('data-src'));
      } else {
        loadFromServer = true;
        if($('#ajax-modal').length) $('#ajax-modal').remove();
        $('body').append('<div id="ajax-modal" class="hidden"></div>');
        modal = $('#ajax-modal');
      }
      var opt = {
        modal: true,
        title: obj.attr('data-title') || 'No Title',
        width: obj.attr('data-width') || '360',
        dialogClass: 'strings-modal',
        height: 'auto',
        open: function() {
          if(!$('body').hasClass('blur')) $('body').addClass('blur');
          if($('.ui-dialog .autocomplete').length) $('.ui-dialog input.maininput').blur().parents('.ui-dialog-content').css('overflow','visible');
        },
        close: function() {
          if($('body').hasClass('blur')) $('body').removeClass('blur');
          if($('.ui-dialog .autocomplete').length) $('.ui-dialog input.maininput').parents('.ui-dialog-content').css('overflow','auto');
          modal.find('.cta:not(".cancel,.primary")').unbind();
          if(obj.hasClass('reload')) {
            location.reload();
          }
        }
      };
      var loadCallback = function(rescanDom) {
          rescanDom = typeof rescanDom !== 'undefined' ? rescanDom: true;
          $(this).dialog("option", "position", ['center', 'center'] );
          if(rescanDom){
            strings.ui.tables.attach('.strings-modal table[data-type="datatable"]:not(.loaded)');
            strings.associations.attach('.association');
            strings.events.forms();
            strings.events.keypress();
          }
          modal.find('.cta:not(.cancel,.primary)').bind('click', function() {
            modal.dialog('close');
          });
      };
      if(loadFromServer) {
        modal.dialog(opt).dialog('open').load(obj.attr('data-src'),loadCallback);
      }
      else {
        modal.dialog(opt).dialog('open');
        loadCallback(false);
      }
    },
    tables: {
      attach: function(filter) {
        $(filter).each(function() {
          $(this).dataTable({
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[2, 10, 25, 50, 100, 200, -1], [2, 10, 25, 50, 100, 200, "All"]],
            "iDisplayLength": parseInt($(this).attr('data-length')) || 10,
            "oLanguage": {
              "sSearch": "",
              "sEmptyTable": $(this).attr("data-empty-table") === undefined ? "No data available": $(this).attr("data-empty-table"),
              "sProcessing":"<img class='loading' src='/img/loading.gif' />"
            },
            "sDom": '<"top"f>rt<"bottom"p><"clear">',
            "bAutoWidth": $(this).attr('data-auto-width') === undefined ? false: ($(this).attr('data-auto-width') == 'true'),
            "bPaginate": $(this).attr('data-paginate') === undefined ? true: ($(this).attr('data-paginate') == 'true'),
            "bFilter": $(this).attr('data-search') === undefined ? false: ($(this).attr('data-search') == 'true'),
            "bProcessing": $(this).attr('data-processing') === undefined ? false: ($(this).attr('data-processing') == 'true'),
            "bServerSide": ($(this).attr("data-src") === undefined || $(this).attr('data-src') == 'false' ? false: true),
            "sAjaxSource": ($(this).attr("data-src") === undefined || $(this).attr("data-src") == 'false' ? null: $(this).attr("data-src")),
            "fnInitComplete": function(oSettings) {
              var parent = $(this).parents('.dataTables_wrapper');
              if($(this).attr('data-cta')) {
                parent.find('.top').prepend($(this).attr('data-cta'))
              }
              if($(this).attr('data-title') && $(this).attr('data-title') != 'false') {
                parent.find('.top').prepend('<h2>'+$(this).attr('data-title')+'</h2>');
                if(!$(this).attr('data-cta')) parent.find('h2').css('display','inline');
              }
              if($(this).attr('data-raw-title') && $(this).attr('data-raw-title') != 'false') {
                var element = $($(this).attr('data-raw-title'));
                parent.find('.top').prepend(element);
                //if(!$(this).attr('data-cta')) element.css('display','inline');
              }
              if($(this).attr('data-search') === undefined || $(this).attr('data-search') == 'true'){ 
                parent.find('.dataTables_filter input').attr('placeholder','Search');
              }
              var parent = $(this).parents('.dataTables_wrapper');
            }
          });
          $(this).addClass('loaded');
        });
      }
    },

    recent_activites: function() {
      var refresh = function() {
        $('.recent-activities .cta.refresh').click();
      };
      refresh();
      setInterval(refresh,120000);
    }

  },
  
  events: {
    clicks: function() {
      // close message boxes
      $('ul#messages li a.close').live('click', function() { $(this).parent().remove() });
      // tooltips
      $('.tooltip').tooltip({ position: { my: "left+2 top+14", at: "left top+14" } });
      // modal windows
      $('.modal').live('click',function(){strings.ui.modal($(this))});
      // form ctas
      $('form .cta.submit:not(.disable-autosubmit)').live('click',function(){ $(this).closest('form').submit() });
      $('.recent-activities .cta.refresh').live('click',function() {
        var container = $(this).closest('.recent-activities');
        container.find('ul').load(container.attr('data-src'));
      });
    },
    keypress: function() {
      $("input:not(.disable-autosubmit)").keypress(function (e) {
        if (e.which == 13) {
          e.preventDefault();
          $(this).closest('form').submit();
        }
      });
    },
    forms: function() {
      $('form.disabled').each(function(){
        $(this).submit(function(e){
          e.preventDefault();
        });
      });
	  $('form.ajax').each(function(){
		$(this).submit(function(e){
		  e.preventDefault();
		  $.ajax({
		    type: (($(this).attr('method') === undefined || $(this).attr('method').toLowerCase() == 'post') ? 'post': 'get'),
			url: $(this).attr('action'),
			data: $(this).serialize(),
			success: function(data, textStatus){
			  if(data.redirectUri !== undefined){
			    window.location.href = data.redirectUri;
			  }
			  else {
			  	var messageElement = '<li>' + data.message + '</li>';
			    var messageClass = "success";
			    if(data.isError){
			      messageClass = "error";
			    }
			    $("#notice").empty();
			    $(messageElement).appendTo("#notice").addClass(messageClass);
			  }
		    }
		  })
		  .error(function(jqXHR,textStatus){
		    console.log(textStatus);
		  });
		});
	  });
      // count type validation
      $('.cta.disabled').each(function() {
        $(this).parents('form').find(':password').last().keyup(function() {
          if($(this).val().length > 5){
            $(this).parents('form').find('.disabled').toggleClass('disabled not-disabled');
            if($(this).parents('form').find(':password').length > 1) {
              var p1 = $(this).parents('form').find(':password').first().val();
              var p2 = $(this).parents('form').find(':password').last().val();
              if(p1 !== p2) $(this).parents('form').find('.not-disabled').toggleClass('not-disabled disabled');
            }
          } else {
            $(this).parents('form').find('.not-disabled').toggleClass('not-disabled disabled');
          }
        });
      });
      // auto-complete
      $('.autocomplete').each(function() {
        $(this).autocomplete({
          source: $(this).attr('data-src')
        });
      });
      // FCBK auto-complete
      $('.autocomplete-tag').not('.example > .autocomplete-tag').each(function() {
        var json = $(this).attr('data-src') || null;
        var placeholder = $(this).attr('data-placeholder') || 'Enter a tag...';
        var width = $(this).attr('data-width') || '500px';
        var options = {
          json_url: json,
          width: width,
          cache: false,
          height: "10",
          newel: true,
          addontab: true,
          addoncomma: true,
          firstselected: false,
          filter_case: false,
          filter_selected: false,
          filter_begin: false,
          filter_hide: true,
          complete_text: placeholder,
          select_all_text:  null,
          maxshownitems: 30,
          maxitems: 150,
          oncreate: null,
          onselect: null,
          onremove: null,
          attachto: null,
          delay: 200,
          input_tabindex: 0,
          input_min_size: 1,
          input_name: "",
          bricket: true
        }
        $(this).fcbkcomplete(options);
      });
    }
  },

  notifications: {
    unexpectedError: function(){
      strings.notifications.alert('We encountered an unexpected error. Please refresh the page.');
    },
    alert: function(msg){
      strings.notifications.notify(msg,'error',true);     
    },
    notify: function(msg,type,empty){
      type = type === undefined ? 'error': type;
      empty = empty === undefined ? true: empty;
      if($('#notice').length > 0 || $('#messages').length > 0) {
        var notifContainer = $('#notice').length > 0 ? $('#notice') : $('#messages');
        notifContainer.empty();
        notifContainer.append("<li class='" + type + "'>" + msg + "</li>");
      }
      else {
        alert(type + ":" + msg);
      }
    },
    empty: function(){
      $('#notice').empty();
    }
  },

  associations: {
    attach: function(filter){
      $(filter).each(function(){
        var container = $(this);
        $(this).find("input:text").keypress(function(e){
          if(e.which == 13){
            container.find(".cta.add").click();
          }
        });
        $(this).find('.cta.add').live('click',function(e){
          var table = container.find('table');
          var dataTable = table.dataTable();
          var input = container.find('input');
          var name = input.val();
          strings.notifications.empty();
          input.blur();
          $.ajax({
            type: "post",
            url: container.attr('data-src-add'),
            data: {
              "name": name
            },
            dataType: 'json',
            success: function(data, textStatus){
              if(!data.isError){
                input.val("");
                dataTable.fnReloadAjax();
              }
              else {
                strings.notifications.alert(data.message);
              }
            }
          })
          .error(function(jqXHR,textStatus){
            strings.notifications.unexpectedError();
            console.log(textStatus);
          });
        });
        $(this).find('.action.remove').live('click',function(e){
          var table = container.find('table');
          var dataTable = table.dataTable();
          strings.notifications.empty();
          $.ajax({
            type: "post",
            url: container.attr('data-src-remove'),
            data: {
              "id": $(this).attr('data-id')
            },
            success: function(data, textStatus){
              dataTable.fnReloadAjax();
            }
          })
          .error(function(jqXHR,textStatus){
            strings.notifications.unexpectedError();
            console.log(textStatus);
          });
        });
      });
    },
    disable: function(filter){
      $(filter).each(function(){
        $(this).addClass('disabled');
        $(this).find('.cta.add').addClass('disabled');
        $(this).find('input:text').prop('disabled',true);
      });
    },
    enable: function(filter){
      $(filter).each(function(){
        $(this).removeClass('disabled');
        $(this).find('.cta.add').removeClass('disabled');
        $(this).find('input:text').prop('disabled',false);
      });
    },
  }
  
};

$(document).ready(strings.client.init());

$(window).load(strings.client.loaded());
