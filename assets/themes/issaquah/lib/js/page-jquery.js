jQuery(document).ready(function($) {
    $('.sidebar-primary .widget_text .widget-title').lettering('words');    
    $(".sidebar-content .content-sidebar-wrap main.content,.sidebar-content .content-sidebar-wrap aside.sidebar-primary").equalHeightColumns(
        {checkHeight: 'innerHeight'}
    );
    
    //do some little stuff for parralaxing
    // init controller
    var controller = new ScrollMagic({globalSceneOptions: {triggerHook: "onEnter", duration: $(window).height()*2}});

    // build scenes
    new ScrollScene()
        .setTween(TweenMax.fromTo("#page-title-area .banner", 1, {css:{'background-position':"50% 40%"}, ease: Linear.easeNone}, {css:{'background-position':"50% -40%"}, ease: Linear.easeNone}))
        .addTo(controller);
    //do some nifty stuff for the menu
    $('.widget_advanced_menu .menu>li>.sub-menu>li.current-menu-item,.widget_advanced_menu .menu>li>.sub-menu>li.current-menu-ancestor').addClass('open');
    $('.widget_advanced_menu .menu>li>.sub-menu>li').prepend(function(){
        if($(this).hasClass('menu-item-has-children')){
            if($(this).hasClass('open')){
                return '<i class="fa fa-minus"></i>';
            } else {
                return '<i class="fa fa-plus"></i>';
            }
        } else {
            return '<i class="fa"></i>';
        }
    });
    $('.widget_advanced_menu .menu>li>.sub-menu>li>i.fa').click(function(){
        var old = $('.widget_advanced_menu .menu>li>.sub-menu>li.open');
        var cur = $(this).parent();
        if(cur.hasClass('open')){
            cur.removeClass('open').find('i').removeClass('fa-plus').addClass(function(){
                if($(this).parent().hasClass('menu-item-has-children')){
                    return 'fa-minus';
                }
            });
        } else {
            old.removeClass('open').find('i').removeClass('fa-minus').addClass(function(){
                if($(this).parent().hasClass('menu-item-has-children')){
                    return 'fa-plus';
                }
            });
            cur.addClass('open').find('i').removeClass('fa-plus').addClass(function(){
                if($(this).parent().hasClass('menu-item-has-children')){
                    return 'fa-minus';
                }
            });
        }
    });
    
    $(window).scroll(function () {
        $(".sidebar-content .content-sidebar-wrap main.content,.sidebar-content .content-sidebar-wrap aside.sidebar-primary").equalHeightColumns(
            {checkHeight: 'innerHeight'}
        );
    });
});