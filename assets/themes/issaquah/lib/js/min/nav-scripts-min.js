jQuery(document).ready(function(){function e(e,s,a,n){var l;e.clone().attr("id",a).removeClass().attr("class",n).appendTo(s),l=s.find("> ul"),l.find(".menu_slide").remove(),l.find("li:first").addClass("sf_first_mobile_item"),l.find("li.menu-item-has-children>a").append('<span class="toggle-switch pull-right"><i class="fa fa-2x fa-angle-down"></i></span>'),l.find("li.menu-item-has-children .sub-menu").hide(),l.find("li.current-menu-ancestor>.sub-menu").show(),l.find("li.current-menu-ancestor").removeClass("closed").addClass("opened"),l.find("li.current-menu-ancestor>a>span>i").removeClass("fa-angle-down").addClass("fa-angle-up"),s.click(function(){return jQuery(this).hasClass("closed")?(jQuery(this).removeClass("closed").addClass("opened"),l.slideDown(500)):(jQuery(this).removeClass("opened").addClass("closed"),l.slideUp(500)),!1}),s.find("a").click(function(e){e.stopPropagation()})}if(jQuery("#menu-primarynav").wrap('<div id="nav-response" class="nav-responsive">'),jQuery("#nav-response").append('<a href="#" id="pull" class="closed"><strong>MENU</strong></a>'),"none"!==jQuery("#pull").css("display")){var s=jQuery(".nav-responsive").find("li.search");jQuery("#pull").before(s)}e(jQuery(".nav-responsive>ul"),jQuery("#pull"),"mobile_menu","sf_mobile_menu"),jQuery("#pull li .toggle-switch").click(function(e){return e.preventDefault(),jQuery(this).parent("a").parent("li").hasClass("opened")?(jQuery(this).children("i").removeClass("fa-angle-up").addClass("fa-angle-down"),jQuery(this).parent("a").parent("li").removeClass("opened").addClass("closed"),jQuery(this).parent("a").parent("li").find(".sub-menu").slideUp(500).parent("li").removeClass("opened").addClass("closed").find("i").removeClass("fa-angle-up").addClass("fa-angle-down")):(jQuery(this).children("i").removeClass("fa-angle-down").addClass("fa-angle-up"),jQuery(this).parent("a").parent("li").removeClass("closed").addClass("opened"),jQuery(this).parent("a").parent("li").find(".sub-menu").slideDown(500),jQuery(this).parent("a").parent("li").find(".sub-menu>li>.sub-menu").hide(),jQuery(this).parent("a").parent("li").find(".sub-menu>li").addClass("closed")),!1})});