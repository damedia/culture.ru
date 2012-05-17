$(function(){
	$('input[type="text"],input[type="password"],textarea').each(function(){
		if(!$(this).closest('#site-search').length)
			$(this).wrap('<span class="inp-txt" />');
	});
	
	$('input[type="button"],input[type="reset"],input[type="submit"],button').each(function(){
		if(!$(this).closest('#site-search').length)
			$(this).wrap('<span class="inp-btn" />');
	});
	
	$('.inp-txt,.inp-btn').each(function(){
		$(this).addClass($(this).children().attr('class'));
	});

	
	//
	var content = $('#content');
	var siteSearch = $('#site-search');
	var topNav = $('#nav-top');

	/*
	var updateHeader = function() {
		// site search
		if(content.width() > 990) {
			siteSearch.find('.inp-txt input').css('width', content.width() * 9 / 100);
		} else {
			siteSearch.find('.inp-txt input').css('width', 90);
		}
		
		topNav.css('width', content.width() - siteSearch.width());
	};
	
	$(window).resize(updateHeader);
	
	updateHeader();
	*/
	
	// top navigation
	topNav.find('a').each(function(){
        if ($(this).attr('href') == document.location.pathname) {
            topNav.find('a').parent().removeClass('current').removeClass('prevhover')
            $(this).parent().addClass('current').prev().addClass('prevhover')
        }
		if(/#/.test($(this).attr('href'))) {
			$(this).click(false);
		}
	})
	
	topNav.find('td').hover(function(){
		if(/#/.test($(this).find('a:first').attr('href')))
			return;
		
		$(this).addClass('hover').prev().addClass('prevhover');
	},function(){
		$(this).removeClass('hover').prev().removeClass('prevhover');
	});
	
	topNav.find('td:last').css({backgroundImage:'none'});
	topNav.find('td.current').prev().addClass('prevhover');
	
	
	// fixes
	$('.nav-tags li:first-child').addClass('first');

	
	// news inner navigation
	var newsInnerNav = $('#nav-inner');
	if(!newsInnerNav.hasClass('link-no-handle')) {
		newsInnerNav.find('> ul > li > a').click(function() {
			if($(this).parent().find('ul').length) {
				$(this).parent().toggleClass('current');
				$(this).parent().find('ul').slideToggle();
			}
			if(/#/.test($(this).attr('href'))) {
				return false;
			}
		});
	}
	
	// top online trabslation block
	$('.trans-wrapper-header').click(function() {
        $('.trans-wrapper-body').toggle()
    });
    $('.trans-wrapper').hover(function() {
       
       }, 
    function(){
      $('.trans-wrapper-body').fadeOut(2000);  
    });
    
    if ($('.trans-wrapper').hasClass('trans-wrapper-event')) {
        $('.trans-wrapper-body-text').hide()
        $('.trans-item').show()
    }
    else {
        $('.trans-wrapper-body-text').show()
        $('.trans-item').hide()
    }
	
	// other
	$('.sb-tiser').each(function(){
		$(this).wrap('<div class="sb-tiser-wrap"><div class="sb-tiser-i" /></div>').before('<div class="sb-tiser-bg" />');
	});
	
	$('#lnk-adv-search-filter').click(function(){
		$('.adv-search-filter').slideToggle(200);
		return false;
	});

	$('.nav-show-tabs a').click(function(){
		var tabsWrap = $(this).parents('.show-tabs-wrap'),
			tabsNav = $('.nav-show-tabs',tabsWrap),
			showTab = $(this).attr('href');
		$('li',tabsNav).removeClass('current');
		$('.show-tab',tabsWrap).hide();
		$(this).parent().addClass('current');
		$(showTab).fadeIn(300);
		return false;
	});

	$('.pseudo-select').hover(function(){
		$(this).addClass('pseudo-select-current');
	},function(){
		$(this).removeClass('pseudo-select-current');
	});

	$('.nav-inner-single a').click(function(){
		$('.nav-inner-single li').removeClass('current');
		$(this).parent().addClass('current');
		return false;
	});

	$('.events-list-item').hover(function(){
		$(this).addClass('events-list-item-hover');
	},function(){
		$(this).removeClass('events-list-item-hover');
	});

	$('.lnk-user').prepend('<b class="ico lnk-user-ico" />');
	$('.user-rating').prepend('<b class="ico user-rating-ico" />');

	if($('#lnk-map-slideshow input')){
		$('#lnk-map-slideshow').addClass('inp-cb-checked');
	}
	$('#lnk-map-slideshow input').change(function(){
		if(this.checked){
			$('#lnk-map-slideshow').addClass('lnk-map-slideshow-active');
		}else{
			$('#lnk-map-slideshow').removeClass('lnk-map-slideshow-active');
		}
	});

	$('#inner-filter-more').click(function(){
		$(this).parent().find('fieldset').slideToggle();
	});

	$('.d-select-pseudo li:contains("' + $('.d-select-pseudo div').text() + '")').remove();
	$('.d-select-pseudo div').click(function(){
		$('.d-select-pseudo ul').slideUp();
		var parent	= $(this).parent(),
			list	= $('ul',parent);
		if($(list).is(':visible')){
			$(list).slideUp('fast');
		}else{
			$(list).slideDown('fast');
		}
		$('li',list).click(function(){
			var value = $(this).text();
			$('div',parent).text(value);
			$(list).hide();
			
		});
		return false;
	});
	$('body').click(function(){
		$('.d-select-pseudo ul').hide();
	});

	$('.lnk-subscribe-form').click(function(){
		$('.subscribe-form').slideToggle();
		return false;
	});

	$('.gallery').dGalleryCrop();

	showTime();
});

function showTime(){
	var date = new Date(),
		time = date.getHours()+':'+date.getMinutes();
	$('#header-news-options time').text(time);
}


/*esoldatov*/

$(document).ready(function() {
    $('.l1-title').click(function(){
        $(this).next().slideToggle();
    });
    
if ($('.video-descr-data-fancy').length){    
    $('.video-descr-data-fancy').fancybox();
   } 
    if ($('.events-list-item-video .see-also-list-img').length){
        $('.events-list-item-video .see-also-list-img').fancybox();
    }
    $('.see-also-list-img-fansy').hover(function(){
        $(this).next().css('margin-left' , '185px')
    },function() {
        $(this).next().css('margin-left' , '-9999px')
    });
    
    $('.partner-table tr:nth-child(odd)').css('background','#c8c8c8');
    $('.partner-table tr:nth-child(even)').css('background','#ebebeb');
    $('.partner-table tr:nth-child(odd) td:nth-child(even)').css('background','#ebebeb');
    $('.partner-table tr:nth-child(even) td:nth-child(even)').css('background','#c8c8c8');
    
    $('.partner-table td').hover(function() {
        $(this).find('.img2').css('visibility','visible');
    }, function() {
        $(this).find('.img2').css('visibility','hidden');
    });
    
    
});    
/*$(document).ready(function() {

	var navBar = $('#nav-inner li a')
	
	navBar.each(function(){
	
        if ($(this).attr('href') == document.location.pathname) {
			
            navBar.parent().removeClass('current')
            $(this).parent().addClass('current').prev().addClass('prevhover')
        }
    
    })
	$('.cat ul').hide()
	$('.cat a').click(function() {
		$(this).next().show();
	})

});*/

/**/