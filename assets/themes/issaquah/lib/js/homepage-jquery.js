jQuery(document).ready(function($) {
    var numwidgets = $('#homepage-widgets section.widget').length;
    $('#homepage-widgets').addClass('cols-'+numwidgets);
    var cols = 12/numwidgets;
    $('#homepage-widgets section.widget').addClass('col-sm-'+cols);
    $('#homepage-widgets section.widget').addClass('col-xs-12');
        
    var nav = $('nav.nav-primary');
    if($( window ).width() > 480){
        if ($(this).scrollTop() > 230) {
            nav.addClass("scroll-nav");
            $('header.site-header').css('opacity',1);
        } else {
            nav.removeClass("scroll-nav");
            $('header.site-header').css('opacity',0);
        }
    }
    $(window).scroll(function () {
        
        if($( window ).width() > 480){
            if ($(this).scrollTop() > 230) {
                nav.addClass("scroll-nav");
                $('header.site-header').css('opacity',1);
            } else {
                nav.removeClass("scroll-nav");
                $('header.site-header').css('opacity',0);
            }
        }
    });
});