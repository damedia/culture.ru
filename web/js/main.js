jQuery(document).ready(function(){
    if($.browser.msie && jQuery.browser.version <= "9.0"){
        $('.breadcrumbs li').last().addClass('last-child');
    }
}); //ready
$(function () {
    /* BEGIN flexslider initializations */
    $('.flexslider[data-behave!="nice"]').flexslider({
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
    $('.flexslider[data-behave="nice"]').flexslider({
        animation: "slide",
        controlNav: false,
        slideshow: false,
        start: function(slider){
            var imagesCollection = $("ul.slides li img", slider),
                forcedWidth = slider.attr("data-width"),
                forcedHeight = slider.attr("data-height");

            $.each(imagesCollection, function(){
                var image = $(this);

                //remove weird css padding for <article> wrapper of an image
                image.closest("article").css("padding", "0");

                if (image.width() > forcedWidth) {
                    image.width(forcedWidth);
                    image.height("auto");
                }

                if (image.height() > forcedHeight) {
                    image.height(forcedHeight);
                    image.width("auto");
                }
            });

            //do align images vertically and horizontally after they are properly sized
            $.each(imagesCollection, function(){
                var image = $(this),
                    imageLi = image.closest("li"),
                    alignerHeight;

                if (image.width() < slider.width()) {
                    image.css("margin-left", "auto");
                    image.css("margin-right", "auto");
                }

                if (image.height() < slider.height()) {
                    alignerHeight = (slider.height() - image.height()) / 2;
                    $("<div />").height(alignerHeight).prependTo(imageLi);
                }
            });
        }
    });
    /* END flexslider initializations */

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

        //$('#featured').css({'height': $('img', firstPanel).height()});

        /*Find Max Image Height*/
        $(window).load(function () {
            $('.ui-tabs-panel img').each(function (index) {
                thisImage = $(this);
                imgArray.push(thisImage.height());
                if (imgArray.length == tabsCount) {

                    /*var maxHeight = $('.ui-tabs-nav').height();
                    for (var im = 0; im < imgArray.length; im++) {
                        maxHeight = imgArray[im] > maxHeight ? imgArray[im] : maxHeight;
                    }*/
                    ;
                    //$('.ui-tabs-panel').height(maxHeight);
                    //$('.ui-tabs-panel').width($('.ui-tabs-panel').width());
                    //$('#featured').css('height', maxHeight);

                    startTabs = true;
                }

                maxHeight = ($(".right-column section.block").height() || 486) - 16;
                $('.ui-tabs-panel').each(function() {
                    var $panel = $(this),
                        $image = $("img", this);

                    $panel.height(maxHeight);
                    $image.css("margin-top", ($panel.height() - $image.height()) / 2);
                });
                $("#featured").height(maxHeight);
                startTabs = true;
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
                                        'z-index': 1
                                    })
                                    .fadeIn(1000, function () {
                                        $(this).css('z-index', 0);

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

    $('.in-fancybox-noresize').fancybox({
        beforeShow: function(){
            $('.left-column iframe').hide();
        },
        afterClose: function(){
            $('.left-column iframe').show();
        },
        nextEffect: 'fade',
        prevEffect: 'fade',
        autoResize: false,
        fitToView: false,
        scrolling: 'no'

    });

	if($('.virt-museum-instr').length > 0) {
		$('.virt-museum-instr').fancybox({
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


	$('.profile-form select').each(function(){
		$(this).css({'display':'block','visibility':'hidden'});
		$(this).selectgroup();
	})

    $('.subscription-form label').click(function(){
        if($(this).prev('input').is(':checked')) {
            $(this).removeClass('checked');
        } else {
            $(this).addClass('checked');
        }
    })
    $('.subscription-form input[checked]').each(function(){
        $(this).next('label').addClass('checked');
    })

    var proActive = $('.profile-menu li').index($('.profile-menu li.active'));
    $('.profile-right-block:first').addClass('prb-'+proActive);

    var proRightHeight = $('.profile-right').height();
    var proLeftHeight = $('.profile-left').height();
    console.log(proRightHeight);
    console.log(proLeftHeight);
    if(proRightHeight > proLeftHeight) {
        $('.profile-left').height(proRightHeight);
    }

})

$(window).load(function(){
    $('.orange-slider-block .period-block').each(function(){
        var h = $(this).find('h2');
        var blockPad = parseFloat($(this).css("padding-top"));
        var hPad = parseFloat(h.css("padding-top")) + parseFloat(h.css("padding-bottom")) + parseFloat(h.css("margin-top")) + parseFloat(h.css("margin-bottom"));
        var hHeight = h.height();
        var imgHeight = Math.round($(this).find('.period-block_image').height()/2);
        var fdnavHeight = Math.round($('.flex-direction-nav').height()/2);

        // console.log(blockPad, hPad, hHeight, imgHeight, fdnavHeight);
        $('.orange-slider-block .flex-direction-nav').css({'top': blockPad + hPad + hHeight +imgHeight - fdnavHeight});

    });
});

jQuery(document).ready(function(){
    $(".profile-help_icon").click(function(){
        var help_show = $(this).parent();
        if (help_show.hasClass("profile-help-show")) {
            help_show.removeClass("profile-help-show")
            help_show.find(".profile-help_text").hide();
        } else {
            help_show.addClass("profile-help-show");
            help_show.find(".profile-help_text").fadeIn(300); }
    })
}); //ready
