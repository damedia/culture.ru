$(function(){
	
	$('.el-one').hover(function(){
		$(this).addClass('el-one-hovered');
	}, function(){
		$(this).removeClass('el-one-hovered');
	});
	
	
	var scrollPane, api;
	
	$(window).load(function(){
		
		/*var recalcScrollPane = function() {
			var expMaxNum = 0, 
				expThisNum = 0, 
				expMaxLine;
			$('.line').each(function(){
				expThisNum = $(this).find('.el-one').length;
				if (expThisNum > expMaxNum) {
					expMaxNum = expThisNum;
					expMaxLine = $(this);
				}
			})
			
			var expLineLast = expMaxNum - 1;
			var expLineLastLeft =  $('.el-one:eq('+expLineLast+')', expMaxLine).position().left;
			var expLineLastWidth =  $('.el-one:eq('+expLineLast+')', expMaxLine).width();
			$('.exponats-scroll').width(expLineLastLeft + expLineLastWidth + 250);
			/*if (scrollPane) {
				 api.reinitialise();
			}
		};*/
	
		
		$('.exponats-scroll-pane').each(function(){
			scrollPane = $(this).jScrollPane({animateScroll:true});
			api = scrollPane.data('jsp'); 
			var throttleTimeout;
			$(window).bind(
				'resize',
				function()
				{
					if ($.browser.msie) {
						if (!throttleTimeout) {
							throttleTimeout = setTimeout(
								function()
								{
									api.reinitialise();
									throttleTimeout = null;
								},
								50
							);
						}
					} else {
						api.reinitialise();
					}
				}
			);
			
			$('#scroll-right').bind(
				'click',
				function()
				{
					api.scrollByX(200);
				}
			);
			$('#scroll-left').bind(
				'click',
				function()
				{
					api.scrollByX(-200);
				}
			);
			
			scrollPane
				.bind( 
					'mousewheel',
					function (event, delta, deltaX, deltaY) 
					{ 
						api.scrollByX(delta*-100);
						return false;
					} 
				)
				.bind(
				'jsp-scroll-x',
				function(event, scrollPositionX, isAtLeft, isAtRight)
				{
					console.log('Handle jsp-scroll-x', this,
								'scrollPositionX=', scrollPositionX,
								'isAtLeft=', isAtLeft,
								'isAtRight=', isAtRight);
					if (isAtLeft) {
						$('#scroll-left').hide();
					} else if (isAtRight) {
						$('#scroll-right').hide();
					} else {
						$('#scroll-left').show();
						$('#scroll-right').show();
					}			
				}
			)				
		});
	
		$('#carousel img.active').click();
	
	})
	
	
	$('#exp-types').on('click', 'a', function(){
            if (!$(this).parent().hasClass('active')) {
		var type = $(this).attr('href').substr(1);
		$(this).parent().addClass('active').siblings().removeClass();
		$('#exponats-content').removeClass().addClass(type);
		api.reinitialise();
            }
            
            return false;
	})
	
	$('#exp-categories').on('click', '.with-filter a', function(){
		var li = $(this).parent();
		console.log(li.hasClass('filtered'));
		if(li.hasClass('filtered')) {
			li.removeClass('filtered')
			$('#exp-filter').slideUp();
		} else {
			li.addClass('filtered').siblings().removeClass('filtered');
			$('#exp-filter').show();
		}
		
		
		return false;
	})
	
	$('#exp-filter').on('click', '#filter-handle',  function(){
		$('#exp-categories li').removeClass('filtered');
		$('#exp-filter').slideUp();
	})
	
	$('.alphabet').on('click', 'a', function(){
		$(this).toggleClass('active').siblings().removeClass('active');
		return false;
	})
	
	$('#exponats-tags').on('click', '.delete', function(){
		var tag = $(this).prev(),
			tagText = tag.text();
		$(this).parent().remove();
		$('.fiter-results a').filter(function(index) { return $(this).text() === tagText; }).removeClass('active');
	})
	
	$('#exp-filter .fiter-results').on('click', 'a',  function(){
		var tag = $(this),
			tagText = tag.text(),
			newTag;
		
		if(tag.hasClass('active')) {
			$(this).removeClass('active');
			$('#exponats-tags a').filter(function(index) { return $(this).text() === tagText; }).parent().remove();
		} else {
			newTag = $('<li><a href="#">'+tagText+'</a><span class="delete"></span></li>')
			newTag.appendTo('#exponats-tags');
			$(this).addClass('active');
		}
		
		return false;
	})
	
	//$('#carousel').jcarousel({});
	
	$(".jcarousel-container")
	.mousewheel(function(event, delta) {
		if (delta < 0)
			$(".jcarousel-next").click();
		else if (delta > 0)
			$(".jcarousel-prev").click();
		return false; 
	});
	
	
	$('#carousel').on('click', 'img', function(){
	
		var borderWidth = $(this).width() - 4,
			borderHeight = $(this).height() - 4,
			imgBorder = $('<i style="width:'+36+'px; height:'+48+'px; "></i>'),
			fullImageSrc = $(this).attr('data-fullimage'),
			fullImageWidth, fullImageHeight;
			
		$(this).parent().append(imgBorder)
				.siblings().find('i').remove();
		
		$(this).addClass('active')
			   .parent().siblings().find('img').removeClass('active');
			
		$('.main-zoomed-image img').css('opacity',0).attr('src',fullImageSrc );
		fullImageWidth = $('.main-zoomed-image img').width();
		fullImageHeight = $('.main-zoomed-image img').height();
			
		$('.main-zoomed-image img').css({
			'opacity':1,
			'marginLeft':-fullImageWidth/2,
			'marginTop':-fullImageHeight/2
		})
	})
	
	var image, imageWidth,  imagePos, popupImage, imgPosLeft, imgPosTop, popupTimeout;
	
	$('#carousel').on('mouseenter', 'li', function(){
		image = $(this).find('img');
		imagePos = image.offset();
		imgPosLeft = imagePos.left;
		imgPosTop = imagePos.top - 90;
		imageWidth = image.width();
		popupImage = $('<img src="'+image.attr('src')+'" alt="" class="popup-image" style="opacity:0;top:'+imgPosTop+'px; left:'+imgPosLeft+'px" id="image_'+image.index()+'" />');
		$('.popup-image').remove();
		$('body').append(popupImage);
		imgPosLeft = imgPosLeft + 20 - popupImage.width()/2;
		popupImage.css({'left':imgPosLeft, 'opacity':'1'});
		
	});
	
	$('#carousel').on('mouseout', 'li', function(){
		$('.popup-image').remove();
	})
	
	
	$('#details-btn').on('click', function(){
		if(!$(this).data('opened')){
			$('#exponats-details').show();
			$('#exponat-main-container').hide();
			$(this).data('opened', true);
			$(this).text('Спрятать детали');
		} else {
			$('#exponats-details').hide();
			$('#exponat-main-container').show();
			$(this).data('opened', false);
			$(this).text('Детали');
		}
		return false;
	})
	
	
	
})