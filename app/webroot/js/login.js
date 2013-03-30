var account = {
  
  click : function() {
    // cta submit buttons
    $('form .cta.submit').click(function(){ $(this).closest('form').submit() });
  },

  keypress : function() {
    $(':input').keypress(function (e) {
      if (e.which == 13) {
        e.preventDefault();
        $(this).closest('form').submit();
      }
    });
  },
  
  tabs : function() {
    $('#tabs a, .cta[data-id]').live('click',function() {
      $('form div[data-id]').hide();
      $('form div[data-id='+$(this).attr('data-id')+']').show();
      $('#tabs a.active').removeClass('active');
      $('#tabs a[data-id='+$(this).attr('data-id')+']').addClass('active');
    });
  },
  
  wallpaper : function() {
    var w = ($('#preload img').width() / $(window).width()) * 10;
    var h = ($('#preload img').height() / $(window).height()) * 10;
    if(h < w) $('body').attr('class','portrait');
    if(h > w) $('body').attr('class','landscape');
  },
  
  init : function() {
    account.tabs();
    account.keypress();
    account.click();
    if($('.loading').length){
      var preload;
      preload = setInterval(function() {
        if($('div#preload img').height() > 300) {
          account.wallpaper();
          clearInterval(preload);
        } 
      }, 1500);
    }
  }
  
};

$(window).load(account.init()).resize(function() {
  setTimeout(function() {
    account.wallpaper();
  }, 50);
});