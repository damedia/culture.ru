$(function(){
	$(window).load(function() {
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
		 
	  
	});
	
	
		$.datepicker.setDefaults($.datepicker.regional[ "ru" ]);
		 $("#datapicker").datepicker({
			showOn: 'button',
			 buttonImage: 'images/button_cal.gif',
			 buttonImageOnly: true,
			 dateFormat: 'dd.mm.yy',
			 
			 showOtherMonths: true,
             selectOtherMonths: true,
			 onSelect: function(dateText, inst) {
					   $(this).hide();
					   console.log(dateText);
					   $('a.clicked').html(dateText).removeClass('clicked');
			 }
		 
		 }).hide();

		 // Show the date picker
			 $(".dates-chooser > a").click(function() {
				$("#datapicker").show();
				$(this).addClass('clicked');
			 return false;
		 });

	
	
	/*NEWS SLIDER*/
	if($("#featured").length > 0) {
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
	
	}
	
	
	$('.more').on('click', $(this), function(){
		$('.events-wall-one-wrap').show();
		return false;
	})
	
	$('input#search-txt').on('click', $(this), function(){
		if($(this).data('opened')) {
			$(this).parent('form').next('div.search-checkboxes').hide();
			$(this).data('opened', false);
		} else {
			$(this).parent('form').next('div.search-checkboxes').show();
			$(this).data('opened', true);
		}
		
	})
	
	$('select.uni').selectgroup();
//    $('select.uni').select2();
	
	$('#category-chooser').on('click', 'a', function(){
		var id = $(this).attr('href');
		$(this).addClass('active').siblings().removeClass('active');
		$(id).show().siblings('.tab').hide();
		
		return false;
	})
	
	
	$('.museum-image, .plitka-name').on('click', $(this), function(){
		$(this).closest('.plitka-one-wrap').toggleClass('plitka-one-wrap-opened');
	})
	
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
	
	$('.trad-line-nav > li > a').click(function(){
		var tradLink = $(this).attr('href');
		$(tradLink).show().siblings('.trad-line-content').hide();
		$(this).parent().addClass('active').siblings().removeClass('active');
		
	})
	
	
		$('#video-list').flexslider({
			animation: "slide",
			
			slideshow: false,
			itemWidth: 281,
			itemMargin: 37
		  });
		  
		  
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
	
})
