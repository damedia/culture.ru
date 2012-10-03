
var chronNav = $('.chronicles-nav-wrap'),
	chronNavUlSub = chronNav.find('ul.chronicles-nav ul'),
	chronNavHeight  = chronNav.height(),
	chronNavWidth  = chronNav.width(),
	wrapperMar = $('.wrapper').css('marginBottom');	
	
$(function(){
	
	resizeChronicals(chronNav,chronNavUlSub);

	function resizeChronicals(chronNav,chronNavUlSub) {
		if(( $('body').hasClass('big-font') || $('body').hasClass('huge-font') ) && chronNav.length > 0) {
			chronNavUlHeight = chronNav.find('ul.chronicles-nav').height();
			chronNavUlSubHeight = chronNavUlSub.height();
			chronNavWidth  = chronNav.width();
			chronNavUlSub.css({'top':chronNavUlHeight+10});
			chronNav.css({'height':chronNavUlSubHeight+chronNavUlHeight+20,'width':chronNavWidth});
			
		}
	};
	
	$(window).resize(function(){
		resizeChronicals(chronNav,chronNavUlSub);
	})
	
	
	$(window).scroll(function(){
		chronNav.css({'position':'relative','visibility':'hidden', 'width':'auto', 'left':0, 'top':0 });

		var chronPos = chronNav.position(),
		windowTop = $(window).scrollTop(),
		windowLeft = $(window).scrollLeft();
		
		if(chronPos.top <= windowTop) {
			chronNav.css({'left':chronPos.left-windowLeft, 'top':0, 'position':'fixed', 'width':chronNavWidth});
			$('.chronicals').css({'position':'relative','top':chronNavHeight});
			$('.wrapper').css({'marginBottom':0});
		} else {
			chronNav.css({'position':'relative', 'width':'auto', 'left':0, 'top':0});
			$('.chronicals').css({'top':0});
			$('.wrapper').css({'marginBottom':wrapperMar});
		}
		
		chronNav.css({'visibility':'visible'});
	});
	
	
	$('li li a', chronNav).click(function(){
		$(this).parent().addClass('active')
				.siblings().removeClass('active');
			
	})
	
	$('> ul > li > a',chronNav).click(function(){
		var chronLink = $(this).attr('href');
		$(this).parent().addClass('active')
				.siblings().removeClass('active');
				
		$(chronLink+'_cent').show().siblings('.cent-tab').hide();
		window.location = '#content';
		return false;
	})
	
	resizeChronicals(chronNav,chronNavUlSub);
	
	
	
})
