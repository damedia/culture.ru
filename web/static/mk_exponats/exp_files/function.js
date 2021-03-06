$(document).ready(function () {


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

        $('.active').removeClass('active');
        $('a[href=#' + $active.attr('id') + ']').parent().addClass('active');

       // console.log($('a[href=#' + $active.attr('id') + ']'));
    });
	
	
	
	
 
	
	

    // Register each section as a waypoint.
    $('.wayponits-area > .time-line-content').waypoint({offset:'50%'});


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
            'scrollTop':$target.offset().top
        }, 500, 'swing', function () {
            window.location.hash = target;
        });

    });

    var
        $discusitemTab = $('#discusitem-tab'),
        $discusitemTabs = $discusitemTab.find('li'),
        $discusitemContent = $('.discusitem-content');

    $discusitemTabs.click(function () {
        var indexElem = $(this).index();
        $discusitemContent.hide();
        $discusitemContent.eq(indexElem).fadeIn();
        $discusitemTabs.removeClass('active');
        $(this).addClass('active');
        return false;
    }).eq(0)


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

    /*Atlas tabs*/
    $('.filter-tabs-titles li a, .filter-sub-tabs-titles li a').click(function(){
        var href = $(this).attr('href');
        $(this).parent().addClass('active')
                        .siblings().removeClass('active');
        $(href).show().siblings('div').hide();
        return false;
    })

    if ($('.uniform').length > 0) {
        $('.uniform').uniform();
    }

    if ($('.draggable').length > 0) {
        $('.draggable').draggable();
    }

    $('.filter-checkboxes label').click(function (e) {
        if (e.target.nodeName == 'LABEL') {
            $(this).toggleClass('checked');
        }
    })
    if ($("#calendar").length > 0) {
        $('.calendar-title').click(function () {
            $(this).next('#calendar').slideToggle();
        })

        $.datepicker.setDefaults($.datepicker.regional[ "ru" ]);
        $("#calendar").datepicker({showOtherMonths:true});
    }
	if ($("#news-dates-filter").length > 0) {
        var calendarTriggerImg;
        if (mkApp.locale === 'ru') {
            $.datepicker.setDefaults($.datepicker.regional[ "ru" ]);
            calendarTriggerImg = '/img/calendar-trigger.png';
        } else if (mkApp.locale === 'en') {
            calendarTriggerImg = '/img/calendar-trigger-en.png';
        }
        $(".calendar-input").datepicker({
			showOtherMonths:true,
			showOn: "button",
			buttonImage: calendarTriggerImg,
			buttonImageOnly: true
		});
	}
    /*$('.ppa-block').hover(function(){
     $(this).find('.ppa-over').show();
     },
     function(){
     $(this).find('.ppa-over').hide();
     });*/

    /*news filter*/

    $('.news-filter-list label').click(function (e) {
        if (e.target.nodeName == 'INPUT') {
            $(this).toggleClass('checked');
        }
    })
    $('.news-filter-header').click(function () {
        $(this).next('.news-filter-list').slideToggle();
    })

    /*land filter*/
    $('.lang-switch li.active').click(function () {
        if ($(this).data('visible')) {
            $(this).siblings().hide();
            $(this).data('visible', false);
        }
        else {
            $(this).siblings().show();
            $(this).data('visible', true);
        }
    })

    $("#filter .lvl-1 input:checkbox").click(function () {
        var thisList = $(this).closest('li'),
            thisInputs = $('li input:checkbox', thisList);
        if ($(this).attr('checked') == 'checked') {
            thisInputs.attr('checked', 'checked');
        }
        else {
            thisInputs.attr('checked', false);

        }
    });
    /*MENU MORE*/
    $('.navmenu_more > a').click(function () {
        var hiddenList = $(this).parent().find('ul');
        hiddenList.slideToggle();
        $(this).parent().toggleClass('opened');
        return false;
    })
    /*
     var resizePpa = function(){
     var ppaImg = $('.ppa-block img.news-image-frame'),
     ppaImgheight = ppaImg.height();
     $('.ppa-over').css({'height':ppaImgheight});
     $('.ppa-over a').css({'height':ppaImgheight-40});

     };

     $(window).resize(function(){
     resizePpa();
     });
     $(window).load(function(){
     resizePpa();
     });	*/


    /*Expandable*/
    $('.to_expand_handler a').click(function () {
        var handler = $(this),
            blockToExpand = handler.parent().next('.to_expand_block');


        if (handler.hasClass('expanded_handler')) {
            handler.html(handler.data('text'));
        }
        else {
            handler.data('text', $(this).html());
            handler.html('Close');
        }

        handler.toggleClass('expanded_handler');
        blockToExpand.slideToggle();

        return false;
    })

    /*INDEX TABS*/
    $('.atlas_tabs li').click(function () {
        if (!$(this).hasClass('active')) {
            var thisId = $(this).attr('id');
            $(this).toggleClass('active').siblings().removeClass('active');
            $('#' + thisId + '_tab').show().siblings('div.indextab').hide();
            if (thisId == 'rusObrTab') {
                $(this).closest('.title-block').addClass('orange');
            }
            else {
                $(this).closest('.title-block').removeClass('orange');
            }
        }
    });


    $('#eventsitem-tab span').click(function () {
        var handle = $(this).parent(),
            id = handle.attr('id');

        handle.addClass('active')
            .siblings().removeClass('active');
        $('#' + id + '_tab').show()
            .siblings('.events_tabs').hide();
    });

    $('#rusObrTab_tab .rusObr-list-one').hover(
        function () {
            var width = $(this).width(),
                height = $(this).height();

            $(this).addClass('rusHovered').css({'width':width, 'height':height});

        },

        function () {
            $(this).removeClass('rusHovered').css({'width':'auto', 'height':'auto'});
            $(this).parent().css({'height':'auto'});
        }
    );

    /*
     $('#auth-link').click(function(){
     $(this).next('.top_2_reg').slideToggle();
     return false;
     })*/


    /*top scroll*/

    var eventScroller = {
        eventsBlock:$('.time-line-nav'),
        timeLinePos:function () {
            return this.eventsBlock.position();
        },
        eventScroll:function () {
            this.eventsBlock.css({'position':'static', 'visibility':'hidden', 'width':75 });
            //this.eventsBlock.parent().css({'paddingTop':0 });

            var pos = this.eventsBlock.position(),
                windowTop = $(window).scrollTop(),
                windowLeft = $(window).scrollLeft(),
                evBlockWidth = this.eventsBlock.width(),
                evBlockHeight = this.eventsBlock.height();

            if (this.timeLinePos().top < windowTop) {
                this.eventsBlock.css({'left':pos.left - windowLeft, 'top':0, 'position':'fixed', 'width':evBlockWidth});
            }
            else {
                this.eventsBlock.css({'position':'static', 'width':75});
            }

            this.eventsBlock.css({'visibility':'visible'});
        }
    };

    if ($('.time-line-nav').length > 0) {
        $(window).scroll(function () {
            eventScroller.eventScroll();
        });

        $(window).resize(function () {
            eventScroller.eventScroll();
        });
    }
	
	
	/*MENU RESIZE*/
	var navMore = $('.navmenu_more'),
		navMoreEl = $('li', navMore),
		navMenu = $('.navmenu'),
		navMenuEl = $('> li', navMenu),
		navMenuLength = navMenuEl.length,
		navMenuGap,
		navNumber = 0;
		
		
		function menuRecalc(){
			navMenuEl = $('> li', navMenu);
			navMenuLength = navMenuEl.length;
			if ( $('#nav').width() - navMenu.width()  > navMoreEl.eq(navNumber).width() + 28 ) {
				menuResize();
			}
		}
		
		function menuResize() {
			navMore.removeClass('opened');
			navMore.find('ul').hide();
			navMenuGap = $('#nav').width() - navMenu.width();	
			
			navMore.find('ul').css({'display':'block','visibility':'hidden'});

			if ( navMenuGap  > navMoreEl.eq(navNumber).width() + 28 ) {
				navMoreEl.eq(navNumber).insertBefore(navMore);
				navNumber++;
				menuRecalc();
			} else {
				if ( navMenuGap < 0 ) {
					if (navNumber > 0) {
						navMenuEl.eq(navMenuLength-2).prependTo(navMore.find('ul'));
						navNumber--;
					}
				}
			}
			navMore.find('ul').css({'display':'none','visibility':'visible'});
		}
		
		function menuRe() {
			if ( navMore.length > 0) {
				menuRecalc();
				menuResize();
			}
		}
		
//	commented because it broke atlas
	$(window).resize(function () {
		menuRe();
	});
	menuRe();
	
// Reset Font Size
  var originalFontSize = $('.text').css('font-size');
    $(".resetFont").click(function(){
    $('html').css('font-size', originalFontSize);
  });
  // Increase Font Size
  /*$(".spec-vers").click(function(){
    var currentFontSize = $('.text').css('font-size');
    var currentFontSizeNum = parseFloat(currentFontSize, 10);
    var newFontSize = currentFontSizeNum*1.2;
    $('.text').css('font-size', newFontSize);
	$(this).removeClass("spec-vers")
	$(this).addClass("decreaseFont")
	console.log(currentFontSize);
    return false;
  });*/
  // Decrease Font Size
  $(".decreaseFont").click(function(){
    var currentFontSize = $('.text').css('font-size');
    var currentFontSizeNum = parseFloat(currentFontSize, 10);
    var newFontSize = currentFontSizeNum*0.8;
    $('.text').css('font-size', newFontSize);
	$(this).removeClass("decreaseFont")
	$(this).addClass("spec-vers")
	
	
    return false;
  });	
	
	$('.years-tabs-selector a').click(function(){
		var tabName = $(this).attr('href');
		$(this).parent().addClass('active')
				.siblings().removeClass('active');
				
		$(tabName).show().siblings().hide();
		return false;	
	})

	
	imageResize = function(){
		var lecContHeight = $('.lecture-tile-list-image-container:first').height();
		$('.lecture-tile-list-image-container table').css('height', lecContHeight);
	}
	
	$('.lecture-tile-list-image-frame:first').load(function(){
		imageResize();
	})

	$(window).resize(function(){
		imageResize();
	})
	
	$('.trad-line-nav > li > a').click(function(){
		var tradLink = $(this).attr('href');
		$(tradLink).show().siblings('.trad-line-content').hide();
		$(this).parent().addClass('active').siblings().removeClass('active');
		
	})
	
	
	
});
	
	
	

