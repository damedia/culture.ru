jQuery(document).ready(function(){
    if($.browser.msie && jQuery.browser.version <= "9.0"){
        $('.breadcrumbs li').last().addClass('last-child');
    }
}); //ready
$(function () {
    $('.flexslider').flexslider({
        animation: "slide",
        controlNav: false,
        slideshow: false
    });


    $('#thumbs-slider').flexslider({
        animation: "slide",

        slideshow: false,
        itemWidth: 75,
        itemMargin: 5,
        asNavFor: '#image-slider'
    });

    $('#image-slider').flexslider({
        animation: "slide",

        slideshow: false,
        sync: "#thumbs-slider"
    });

    $.datepicker.setDefaults($.datepicker.regional[ $('body').data('locale') ]);
    var datapickerOpts = {
        showOn: 'button',
        buttonImage: 'images/button_cal.gif',
        buttonImageOnly: true,
        dateFormat: 'dd.mm.yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function (dateText, inst) {
            $("#datapicker").datepicker( "destroy" );
            $('a.clicked').html(dateText).removeClass('clicked');
        }

    };
    
    // Show the date picker
    $(".dates-chooser > a").click(function () {
        $("#datapicker").datepicker(datapickerOpts).show();
        $('.search-checkboxes').hide();
        $('.dates-chooser a.clicked').removeClass('clicked');
        $(this).addClass('clicked');
        
        return false;
    });


    /*NEWS SLIDER*/
    /*if($("#featured").length > 0) {
     $("#featured").tabs({
     show: function(event, ui) {
     var lastOpenedPanel = $(this).data("lastOpenedPanel");
     if (!$(this).data("topPositionTab")) {
     $(this).data("topPositionTab", $(ui.panel).position().top)
     }

     if (lastOpenedPanel)
     {
     lastOpenedPanel.css({
     'position':'absolute',
     'top' : 10
     }).show();

     $(ui.panel)
     .hide()
     .css({
     'z-index': 2,
     'top':0
     })
     .fadeIn(1000, function() {
     $(this).css('z-index', '');

     lastOpenedPanel
     .toggleClass("ui-tabs-hide")
     .css({'position': '', 'top':0})
     .hide();

     });
     }

     $(this).data("lastOpenedPanel", $(ui.panel));
     }
     }

     ).tabs('rotate', 3000);

     }*/


    if ($("#featured").length > 0) {

        var imgArray = [];
        var tabsCount = $('.ui-tabs-panel').length;
        var startTabs = false; // Don't Init Tabs until Panel Heights are set.
        var firstPanel = $('.ui-tabs-panel:first');

        $('#featured').css({'height': $('img', firstPanel).height()});

        /*Find Max Image Height*/
        $(window).load(function () {
            $('.ui-tabs-panel img').each(function (index) {
                thisImage = $(this);
                imgArray.push(thisImage.height());
                if (imgArray.length == tabsCount) {

                    var maxHeight = $('.ui-tabs-nav').height();
                    for (var im = 0; im < imgArray.length; im++) {
                        maxHeight = imgArray[im] > maxHeight ? imgArray[im] : maxHeight;
                    }
                    ;
                    $('.ui-tabs-panel').height(maxHeight);
                    $('.ui-tabs-panel').width($('.ui-tabs-panel').width());
                    $('#featured').css('height', maxHeight);

                    startTabs = true;
                }
            });
        })
        /*Tabs*/
        var tabsInt = setInterval(function () {
            if (startTabs) {
                clearInterval(tabsInt);

                $("#featured").tabs({
                        show: function (event, ui) {
                            var lastOpenedPanel = $(this).data("lastOpenedPanel");
                            if (!$(this).data("topPositionTab")) {
                                $(this).data("topPositionTab", $(ui.panel).position().top)
                            }

                            if (lastOpenedPanel) {
                                lastOpenedPanel.css({
                                    'position': 'absolute',
                                    'top': 10
                                }).show();

                                $(ui.panel)
                                    .hide()
                                    .css({
                                        'z-index': 2
                                    })
                                    .fadeIn(1000, function () {
                                        $(this).css('z-index', '');

                                        lastOpenedPanel
                                            .toggleClass("ui-tabs-hide")
                                            .css({'position': '', 'top': 0})
                                            .hide();

                                    });
                            }
                            

                            $(this).data("lastOpenedPanel", $(ui.panel));
                        }
//                        select: function() {
//                                console.log ($(this).attr('rel'))
//                                //window.location.href = $(this).attr('rel');    //go to link from a tag.
//                            }
                    }

                ).tabs('rotate', 3000);
            }
        }, 100);

        $('.aslink').click(function() {
          window.location.href = $(this).attr('rel');
        });

        /*Resize tabs*/
        var endResize;
        $(window).resize(function () {
            clearTimeout(endResize);

            endResize = setTimeout(function () {
                var imgNewArray = [],
                    maxHeight = 0,
                    uiTabsNavHeight = 0,
                    visiblePanel = $('.ui-tabs-panel:visible');

                $('.ui-tabs-panel').css({'height': 'auto', 'width': 'auto'});
                $('#featured').css({'height': $('img', visiblePanel).height()});
                visiblePanel.siblings('.ui-tabs-panel').css('opacity', 0).show();

                /*Find Max image height*/
                $('.ui-tabs-panel img').each(function (index) {
                    thisImage = $(this);
                    imgNewArray.push(thisImage.height());

                })

                maxHeight = $('.ui-tabs-nav').height();
                for (var im = 0; im < imgNewArray.length; im++) {
                    maxHeight = imgNewArray[im] > maxHeight ? imgNewArray[im] : maxHeight;
                }
                ;


                $('.ui-tabs-panel').height(maxHeight);
                $('.ui-tabs-panel').width($('.ui-tabs-panel').width());

                visiblePanel.siblings('.ui-tabs-panel').css('opacity', 1).hide();
                $('#featured').css({'height': maxHeight});


            }, 200);


        })

    }


    $('.more').on('click', $(this), function () {
        $('.events-wall-one-wrap').show();
        return false;
    })

    $('input#search-txt').on('click', $(this), function () {
        if ($(this).data('opened')) {
            $(this).parent('form').next('div.search-checkboxes').hide();
            $(this).data('opened', false);
        }
        else {
            $(this).parent('form').next('div.search-checkboxes').show();
            $(this).data('opened', true);
        }

    })


    var fancySelect = (function() {
        $('select.uni').each(function(){
            $(this).css({'display':'block','visibility':'hidden'});
            $(this).selectgroup();
        })
    })();
    
    
	$('#category-chooser').on('click', 'a', function(){
		var id = $(this).attr('href');
		$(this).addClass('active').siblings().removeClass('active');
		$(id).show().siblings('.tab').hide();
		
		return false;
	})
	
	/*
	$('.museum-image, .plitka-name').on('click', $(this), function(){
		$(this).closest('.plitka-one-wrap').toggleClass('plitka-one-wrap-opened');
	})*/
	
	if($(".iframe").length > 0) {
		$(".iframe").fancybox({
			'width': '100%',
			'height': '100%',
			'autoScale': true,
			'transitionIn': 'none',
			'transitionOut': 'none',
			'type': 'iframe'
		});
	}

    $('.news-fancybox').fancybox({
        'autoScale' : false,
        'nextEffect' : 'fade',
        'prevEffect' : 'fade',
        'nextSpeed' : 1000,
        'prevSpeed' : 1000
    })

    $('.in-fancybox').fancybox({
        beforeShow: function(){
            $('.left-column iframe').hide();
        },
        afterClose: function(){
            $('.left-column iframe').show();
        },
        nextEffect: 'fade',
        prevEffect: 'fade'
    });
	
	if($('.museum-instr-link').length > 0) {
		$('.museum-instr-link').fancybox({
			'width':900,
			'height':900,
			'autoSize':false,
			'transitionIn': 'none',
			'transitionOut': 'none'
		})
	}
	
	$('.trad-line-nav > li > a').click(function(){
		var tradLink = $(this).attr('href');
		$(tradLink).show().siblings('.trad-line-content').hide();
		$(this).parent().addClass('active').siblings().removeClass('active');
		
	});
	
/*	
		$('#video-list').flexslider({
			animation: "slide",
			slideshow: false,
			itemWidth: 281,
			itemMargin: 37
		  });
*/

		  
	$('.tabs-headers').on('click', 'a', function(){
		var tabId = $(this).attr('href');
		$(this).addClass('active')
				.parent().siblings().find('a').removeClass('active');
		$(tabId).show().siblings('.tab').hide();
		return false;
	})
	
	
	/*TIMELINE*/
	
    //waypoints scroll

    // The same for all waypoints
    $('body').delegate('.wayponits-area > .time-line-content', 'waypoint.reached', function (event, direction) {

        var $active = $(this);

        if (direction === "up") {
            $active = $active.prev();

            //  console.log("up");
        }
        /*if (!$active.length) $active = $active.end();*/

        $('.section-active').removeClass('section-active');
        $active.addClass('section-active');
       
        $('a[href=#' + $active.attr('id') + ']').parent().addClass('active');

        // console.log($('a[href=#' + $active.attr('id') + ']'));
    });


    // Register each section as a waypoint.
    $('.wayponits-area > .time-line-content').waypoint({offset: '50%'});


    // Wicked credit to
    // http://www.zachstronaut.com/posts/2009/01/18/jquery-smooth-scroll-bugs.html
    var scrollElement = 'html, body';
    $('html, body').each(function () {
        var initScrollTop = $(this).attr('scrollTop');
        $(this).attr('scrollTop', initScrollTop + 1);
        if ($(this).attr('scrollTop') == initScrollTop + 1) {
            scrollElement = this.nodeName.toLowerCase();
            $(this).attr('scrollTop', initScrollTop);
            return false;
        }
    });

    // Smooth scrolling for internal links
    /*$("a[href^='#']").click(function(event) {*/
    $(".smooth-scroll").click(function (event) {
        event.preventDefault();

        var $this = $(this),
            target = this.hash,
            $target = $(target);

        $(scrollElement).stop().animate({
            'scrollTop': $target.offset().top
        }, 500, 'swing', function () {
            window.location.hash = target;
        });

    });

    var
        $EventsitemTab = $('#eventsitem-tab'),
        $EventsitemTabs = $EventsitemTab.find('li'),
        $EventsitemContent = $('.eventsitem-content');

    $EventsitemTabs.click(function () {
        var indexElem = $(this).index();
        $EventsitemContent.hide();
        $EventsitemContent.eq(indexElem).fadeIn();
        $EventsitemTabs.removeClass('active');
        $(this).addClass('active');
        return false;
    }).eq(0)

    $(window).load(function(){
        $('.event-one_image').each(function(){
            $(this).find('figcaption').width($(this).find('img').width());
        })
    })
    
    $('#appstore-link').click(function(){
        $(this).next('ul').slideToggle();
        return false;
    })
    $('.app_store-block ul a').click(function(){
        $(this).closest('ul').slideUp();
    })
    
    $('body').on('click', $(this), function(e){
        if ($('#datapicker').is(':visible')) {
            var t = $(e.target);
            
            if ( !checkClassPos(t, ['hasDatepicker', 'ui-datepicker-header' ]) ) {
                 $('#datapicker').hide();
                $('.dates-chooser a').removeClass('clicked');
            } 
            
            if ( !checkClassPos(t, ['search-checkboxes' ]) ) {
                 $('.search-checkboxes').hide();
            }
        }
    }); 

    function checkClassPos(element, findClassArr){
        var found = false;
        var parents =  element.parents("*");
        element.parents("*").each(function(){
            for (var i = 0; i < findClassArr.length; i++) {
                if ($(this).hasClass(findClassArr[i])) {
                    found = true;
                    return;
                } 
            }            
        })
        if (element.hasClass(findClassArr[0]))
            found = true;
            
        console.log(found);    
        return found;    
    }   
    
    $(window).load(function(){
    
    
        /*News Image 2:3 Crop*/
        $('.news-image-crop img').each(function(){
            var image  = $(this),
                defaultWidth = image.width(),
                defaultHeight = image.height(),
                proportion  = 1.5,
                type = 'vertical',
                topMargin = 0,
                leftMargin = 0;
                
                if (defaultWidth / 1.5 < defaultHeight) {
                    topMargin = (defaultHeight - defaultWidth / 1.5)/2;
                    $('.news-image-crop').css({'width':defaultWidth,'height':defaultWidth/1.5});
                    image.css({'top':-topMargin});
                } else {
                    leftMargin = (defaultHeight - defaultWidth / 1.5)/2;
                    $('.news-image-crop').css({'width':defaultHeight*1.5,'height':defaultHeight});
                    image.css({'left':-leftMargin});
                }
                
                
        })
    })
    
})
