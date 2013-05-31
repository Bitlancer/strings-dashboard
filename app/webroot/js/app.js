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
      strings.events.keypress();
      strings.ui.actionmenu();
    },
    include : function() {
      $('body > header').load('assets/html/header.html');
      $('body > nav').load('assets/html/nav.html', function(response, status, xhr){
        console.log('Nav loaded via ajax')
      });
    }
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
    modal : function(obj) {
      var modal;
      if(obj.attr('data-src')[0] == '#') {
        modal = $(obj.attr('data-src'));
      } else {
        if($('#ajax-modal').length) $('#ajax-modal').remove();
        $('body').append('<div id="ajax-modal" class="hidden"></div>');
        modal = $('#ajax-modal');
        //modal.load(obj.attr('data-src'));
        //var int = setInterval(function(){ if($(".ui-dialog").dialog("isOpen") === true) clearInterval(int) },100);
      }
      //console.log(modal);
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
        }
      };
      modal.dialog(opt).dialog('open').load(obj.attr('data-src'), function() {
          $(this).dialog("option", "position", ['center', 'center'] );
          strings.ui.tables();
          strings.events.forms();
          strings.events.keypress();
          modal.find('.cta:not(".cancel,.primary")').bind('click', function() {
            modal.dialog('close');
          });
      });
      //setTimeout(function() {
      //  $('.ui-dialog').dialog("option", "position", "center");
      //},5000);
    },
    tables : function() {
      $('table[data-type="datatable"]').not('.example table').not('.loaded').each(function() {
        $(this).dataTable({
          "sPaginationType": "full_numbers",
          "aLengthMenu": [[2, 10, 25, 50, 100, 200, -1], [2, 10, 25, 50, 100, 200, "All"]],
          "iDisplayLength": parseInt($(this).attr('data-length')) || 10,
          "oLanguage": { "sSearch": "","sEmptyTable":"No data available" },
          "sDom": '<"top"f>rt<"bottom"p><"clear">',
          "bServerSide": ($(this).attr("data-src") === undefined ? false : true),
          "sAjaxSource": ($(this).attr("data-src") === undefined ? null : $(this).attr("data-src")),
          "fnInitComplete": function(oSettings) {
            var parent = $(this).parents('.dataTables_wrapper');
            if($(this).attr('data-cta')) parent.find('.top').prepend($(this).attr('data-cta'))
            if($(this).attr('data-title')) parent.find('.top').prepend('<h2>'+$(this).attr('data-title')+'</h2>');
            parent.find('.dataTables_filter input').attr('placeholder','Search');
            if($(this).attr('data-search') === 'false') parent.find('.dataTables_filter input').remove();
          }
        });
        $(this).addClass('loaded');
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
      $('.modal').live('click',function(){strings.ui.modal($(this))});
      // form ctas
      $('form .cta.submit').live('click',function(){ $(this).closest('form').submit() });
      //Assocation ctas
      $('form.association .cta.add').live('click',function(e){
        e.preventDefault();
        var src = $(this);
        var form = src.closest('form');
        var input = form.find('input');
        var name = input.val();
        var notice = form.find("#notice");
        notice.empty();
        $.ajax({
          type: "post",
          url: fielset.attr('data-src-add'),
          data: {
            "name": name
          },
          success: function(data, textStatus){
            if(!data.isError){
              var tbody = form.find("table tbody");
              $(tbody).find('td.blank').parent('tr').remove();
              var element = "<tr><td>";
              element += name;
              element += "<a class='action remove' data-id='" + data.id + "'>Remove</a>";
              element += "</td></tr>";
              $(tbody).append(element);
              input.val("");
            }
            else {
              notice.append("<li class='error'>" + data.message + "</li>");
            }
          }
        })
        .error(function(jqXHR,textStatus){
          console.log(textStatus);
        }); 
      });
      $('form.association .action.remove').live('click',function(e){
        e.preventDefault();
        var src = $(this);
        var form = src.closest('form');
        var notice = form.find("#notice");
        notice.empty();
        $.ajax({
          type: "post",
          url: form.attr('data-src-remove'),
          data: {
            "id": src.attr('data-id')
          },
          success: function(data, textStatus){
            var tbody = src.closest('tbody');
            src.closest('tr').remove();
            if(tbody.find('tr').length == 0){
              tbody.append("<tr><td class='blank'>Add a member above</td></tr>");
            }
          }
        })
        .error(function(jqXHR,textStatus){
          console.log(textStatus);
        });
      });
      $("fieldset.association .cta.add").live('click', function(e){
        e.preventDefault();
        var src = $(this);
        var fieldset = src.closest("fieldset");
        var input = fieldset.find("input[type='text']");
        var name = input.val();
        var tbody = fieldset.find("tbody");
        if(name.length > 0 && tbody.find("input[value='" + name + "']").length == 0){
          $(tbody).find("td.blank").parent("tr").remove();
          var element = "<tr><td>";
          element += name;
          element += "<input type='hidden' name='" + fieldset.attr('data-field-name') + "' value='" + name + "' />";
          element += "<a class='action remove'>Remove</a>";
          tbody.append(element);
        }
        input.val("");
      });
      $('fieldset.association .action.remove').live('click', function(e){
        var src = $(this);
        var tbody = src.closest('tbody');
        src.closest('tr').remove();
        if(tbody.find('tr').length == 0){
          tbody.append("<tr><td class='blank'>Add a member above</td></tr>");
        }
      });
    },
    keypress : function() {
      $("fieldset.association input[type='text']").keypress(function(e){
        if(e.which == 13){
          $(this).closest("fieldset").find(".cta.add").click();
        }
      });
      $("input").closest('fieldset').not('.association').keypress(function (e) {
        if (e.which == 13) {
          e.preventDefault();
          $(this).closest('form').submit();
        }
      });
    },
    forms : function() {
	  $('form.ajax').each(function() {
		$(this).submit(function(e) {
		  e.preventDefault();
		  $.ajax({
		    type: (($(this).attr('method') === undefined || $(this).attr('method').toLowerCase() == 'post') ? 'post' : 'get'),
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
  }
  
};

$(document).ready(strings.client.init());

$(window).load(strings.client.loaded());
