jQuery(document).ready(function($) {	
    $('*:first-child').addClass('first-child');
    $('*:last-child').addClass('last-child');
    $('*:nth-child(even)').addClass('even');
    $('*:nth-child(odd)').addClass('odd');
	
	var numwidgets = $('#footer-widgets div.widget').length;
	$('#footer-widgets').addClass('cols-'+numwidgets);
	$.each(['show', 'hide'], function (i, ev) {
        var el = $.fn[ev];
        $.fn[ev] = function () {
          this.trigger(ev);
          return el.apply(this, arguments);
        };
      });

	$('.nav-footer ul.menu>li').after(function(){
		if(!$(this).hasClass('last-child') && $(this).hasClass('menu-item') && $(this).css('display')!='none'){
			return '<li class="separator">|</li>';
		}
	});
	$('form.gplaceholder .gfield_label').each(function(){
	    var placeholder = $(this).html();
	    if(!$(this).next('.ginput_container').hasClass('ginput_container_radio')){
    	    $(this).addClass('hidden');
            $(this).next('.ginput_container').find('input').attr('placeholder',placeholder.replace(/(<([^>]+)>)/ig,""));
            $(this).next('.ginput_container').find('select option.first-child').html(placeholder.replace(/(<([^>]+)>)/ig,""));
        }
	});
	if($( window ).width() > 480){
        $(".site-header").sticky();
        $("nav.nav-primary").sticky({topSpacing:140});
        $(".notification-bar").sticky({topSpacing:199});
    } else {
        $("nav.nav-primary").sticky({});
    }
    //add element to page
    $('article.first-child').prepend('<div class="text-sizer"><div>Text Size <i class="minus fa fa-minus"></i><i class="plus fa fa-plus"></i></div></div>');
    //$('.text-sizer').sticky({topSpacing:210});
    var size = parseInt($('html').css('font-size'));
    $('.text-sizer .plus').click(function(){
        size = size*1.05;
        $('html').css('font-size',size + 'px');
    });
    $('.text-sizer .minus').click(function(){
        size = size*0.95;
        $('html').css('font-size',size + 'px');
    });
    
    $('a').not('[href*="mailto:"]').each(function () {
        var isInternalLink = new RegExp('/' + window.location.host + '/');
        if ( ! isInternalLink.test(this.href) ) {
            $(this).attr('target', '_blank');
        }
    });

});
