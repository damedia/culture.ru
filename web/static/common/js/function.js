$(document).ready(function(){

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


		$('.filter-checkboxes label').click(function(e){
			if (e.target.nodeName == 'LABEL') {
				$(this).toggleClass('checked');
			}
		})
		
		$('.filter-title').click(function () {
			$('.filter-block').slideToggle();
			return false;
			});	
		
		if ($( "#calendar" ).length > 0) {
			$('.calendar-title').click(function(){
				$(this).next('#calendar').slideToggle();
			})
			
			$.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );
			$( "#calendar" ).datepicker({showOtherMonths: true});
		}
		
		setTimeout(function(){
			$('.obj-o-foto-one img').each(function(){
				var imgThis = $(this),
					imgWidth = $(this).width(),
					imgHeight = $(this).height(),
					imgParent = $(this).parents('li');
				
				imgParent.css({'width':imgWidth, 'height':imgHeight});	
				imgThis.css({'width':imgWidth, 'height':imgHeight});	
				
				var img = new Image();
				img.src = $(this).parent('a').attr('href');
				img.onload = function() {
					imgThis.attr('data-width', this.width);
					imgThis.attr('data-height', this.height);
				}
				imgThis.attr('data-orig-width', imgWidth);
				imgThis.attr('data-orig-height', imgHeight);
				imgThis.attr('data-orig-src', imgThis.attr('src'));
			})
		},100);
		
		$('.obj-o-foto-one img:not(.zoomed)').live('click', function(){
			var imgThis = $(this),
				imgWidth = $(this).width(),
				imgHeight = $(this).height(),
				zoomWidth = $(this).attr('data-width'),
				zoomHeight = $(this).attr('data-height'),
				zoomTop = (zoomHeight-imgHeight)/2,
				zoomLeft = (zoomWidth-imgWidth)/2;
				
			imgThis.attr('src', $(this).parent('a').attr('href'));	
			imgThis.addClass('zoomed').css({'zIndex':2}).animate({'width':zoomWidth,'height':zoomHeight, 'top':-zoomTop,'left':-zoomLeft},100);
			return false;
		});
		
		$('.obj-o-foto-one img.zoomed').live('click', function(){ 
			var imgThis = $(this),
			    imageWidth = imgThis.attr('data-orig-width'),
			    imageHeight = imgThis.attr('data-orig-height');
			
			imgThis.removeClass('zoomed').css({'zIndex':1}).animate({'width':imageWidth,'height':imageHeight, 'top':0,'left':0},100).attr('src', imgThis.attr('data-orig-src'));
			return false;
		})
		
		$('.collapsable-list li').click(function(){
			$(this).toggleClass('opened');
		})
		
		
		if($('#thumbs').length > 0 ) {
			// Initially set opacity on thumbs and add
			// additional styling for hover effect on thumbs
			var onMouseOutOpacity = 0.67;
			$('#thumbs ul.thumbs li').opacityrollover({
				mouseOutOpacity:   onMouseOutOpacity,
				mouseOverOpacity:  1.0,
				fadeSpeed:         'fast',
				exemptionSelector: '.selected'
			});
			
			// Initialize Advanced Galleriffic Gallery
			var gallery = $('#thumbs').galleriffic({
				delay:                     2500,
				numThumbs:                 5,
				preloadAhead:              5,
				enableTopPager:            false,
				enableBottomPager:         false,
				imageContainerSel:         '#slideshow',
				controlsContainerSel:      '#controls',
				captionContainerSel:       '#caption',
				loadingContainerSel:       '#loading',
				renderSSControls:          true,
				renderNavControls:         true,
				playLinkText:              'Play Slideshow',
				pauseLinkText:             'Pause Slideshow',
				prevLinkText:              '&lsaquo; Previous Photo',
				nextLinkText:              'Next Photo &rsaquo;',
				nextPageLinkText:          'Next &rsaquo;',
				prevPageLinkText:          '&lsaquo; Prev',
				enableHistory:             true,
				autoStart:                 false,
				syncTransitions:           true,
				defaultTransitionDuration: 900,
				onSlideChange:             function(prevIndex, nextIndex) {
					// 'this' refers to the gallery, which is an extension of $('#thumbs')
					this.find('ul.thumbs').children()
						.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
						.eq(nextIndex).fadeTo('fast', 1.0);

					// Update the photo index display
					this.$captionContainer.find('div.photo-index')
						.html('Photo '+ (nextIndex+1) +' of '+ this.data.length);
				},
				onPageTransitionOut:       function(callback) {
					this.fadeTo('fast', 0.0, callback);
				},
				onPageTransitionIn:        function() {
					var prevPageLink = this.find('a.prev span').css('opacity', '0.3');
					var nextPageLink = this.find('a.next span').css('opacity', '0.3');
					
					// Show appropriate next / prev page links
					if (this.displayedPage > 0)
						prevPageLink.css('opacity', '1');

					var lastPage = this.getNumPages() - 1;
					if (this.displayedPage < lastPage)
						nextPageLink.css('opacity', '1');

					this.fadeTo('fast', 1.0);
				}
			});

			/**************** Event handlers for custom next / prev page links **********************/

			gallery.find('a.prev').click(function(e) {
				gallery.previousPage();
				e.preventDefault();
			});

			gallery.find('a.next').click(function(e) {
				gallery.nextPage();
				e.preventDefault();
			});
		}
		
		/*filter*/
		$('#filter .lvl-1 li:first > label').addClass('fab-opened');
		$('#filter .lvl-1 li:first > ul').css({'display':'block'});
			
		$('#filter .lvl-1 > li').each(function(){
			if($(this).find('ul').length > 0) {
				$(this).find('label').append('<span />');
			}
		})	
		$('#filter .lvl-2 > li').each(function(){
			if($(this).find('ul').length > 0) {
				$(this).addClass('openable');
			}
		})
			
			
		$('#filter .lvl-1 > li').click(function(e){
			if ( $(this).find('ul').length > 0) {
				var thisHidden = $(this).find('ul:first');
				if (!thisHidden.is(":visible")) {
					$(this).find('label').toggleClass('fab-opened');
						thisHidden.slideDown();
						$(this).siblings().find('ul:first').slideUp().end()
										  .find('label').removeClass('fab-opened');
						
						
				}
			}
			
		})
		
		$('#filter .lvl-2 > li.openable').click(function(e){
			if( e.target.nodeName == 'LI' ) {
				$(this).toggleClass('fab-list-opened');
				$(this).find('ul').slideToggle();
			}
		})
		
		$("#filter .lvl-2 input:checkbox").click(function(){
			var thisList = $(this).closest('li').find('ul'),
				thisInputs  = $('li input:checkbox', thisList);
			
			console.log(thisList);
			if ($(this).attr('checked') == 'checked') {
				thisInputs.attr('checked', 'checked');
				if (!thisList.is(":visible")) {
					$(this).closest('li').addClass('fab-list-opened');
					thisList.slideDown();
				}
			}	
			else {
				thisInputs.attr('checked', false);
				if (thisList.is(":visible")) {
					$(this).closest('li').removeClass('fab-list-opened');
					thisList.slideUp();
				}
			}
		}); 
		
		$("#filter .lvl-1 input:checkbox").click(function(){
			var thisList = $(this).closest('li'),
				thisInputs  = $('li input:checkbox', thisList);
			if ($(this).attr('checked') == 'checked') {
				thisInputs.attr('checked', 'checked');
			}	
			else {
				thisInputs.attr('checked', false);
				
			}
		}); 
		
		
		
		/*news filter*/
		
		$('.news-filter-list label').click(function(e){
			if (e.target.nodeName == 'INPUT') {
				$(this).toggleClass('checked');
			}
		})
		$('.news-filter-header').click(function(){
			$(this).next('.news-filter-list').slideToggle();
		})
		
		/*land filter*/
		$('.lang-switch li.active').click(function(){
			if ($(this).data('visible')) {
				$(this).siblings().hide();
				$(this).data('visible', false);
			} else {
				$(this).siblings().show();
				$(this).data('visible', true);
			}
		})
		
		
		/*Gov*/
		$('.gos-buttons a').click(function(){
			var blockId = $(this).parent('li').attr('id');
			$(this).parent('li').addClass('active').siblings().removeClass('active');
			$('.gos-list li#'+blockId+'_block').show().siblings().hide();
			$('.gos-prims').show();
		})
		
		
		/*Expandable*/
		$('.to_expand_handler a').click(function(){
			var handler = $(this),
				blockToExpand  = handler.parent().next('.to_expand_block');
			;
			
			
			if (handler.hasClass('expanded_handler')) {
				handler.html(handler.data('text'));
			} else {
				handler.data('text', $(this).html());
				handler.html('свернуть');
			}
			
			handler.toggleClass('expanded_handler');
			blockToExpand.slideToggle();
			
			return false;
		})
		
		/*top scroll*/
		
		var eventScroller  = {
			eventsBlock: $('#time-line-events'),
			timeLinePos: function(){
				return this.eventsBlock.position();
			},
			eventScroll: function(){
				this.eventsBlock.css({'position':'static','visibility':'hidden', 'width':'auto' });
				this.eventsBlock.parent().css({'paddingTop':0 });
				
				var pos = this.eventsBlock.position(),
					windowTop = $(window).scrollTop(),
					windowLeft = $(window).scrollLeft(),
					evBlockWidth = this.eventsBlock.width(),
					evBlockHeight = this.eventsBlock.height();
				
				if (this.timeLinePos().top < windowTop) {
					this.eventsBlock.css({'left':pos.left-windowLeft, 'top':0, 'position':'fixed', 'width':evBlockWidth})
							.parent().css('paddingTop',evBlockHeight+40);
				} else {
					this.eventsBlock.css({'position':'static', 'width':'auto'})
							.parent().css('paddingTop',0);
				}
				
				this.eventsBlock.css({'visibility':'visible'});
			}
		};
		
		if($('#time-line-events').length > 0) {
			$(window).scroll(function(){
				eventScroller.eventScroll();
			});
			
			$(window).resize(function(){
				eventScroller.eventScroll();
			});
		}
		
		/*TimeLine selector*/
		$('.time-line-nav > li:not(.inactive) > a').click(function(){
			var thisLi = $(this).parent(),
				thisId = thisLi.attr('id');
			
			thisLi.addClass('active').siblings().removeClass('active');
			$('#'+thisId+'_tab').show().siblings('.time-line-content').hide();
			
			return false;
		})
		
		$('.time-line-nav li li a').click(function(){
			var blockId = $(this).attr('href');
			if(blockId != 'undefined') {
				var blockPos = $(blockId).position();
				if ($(blockId).parent().hasClass('time-line-left')) {
					$(window).scrollTop(blockPos.top);
				} else {
					$(window).scrollTop(blockPos.top - $('#time-line-events').height() - 40);
				}	
			}
			return false;
		})
		
		/*Virtual gallery*/
		$('.virtual-overflow-block').hover(function(){
			$(this).addClass('hovered');
		}, function(){
			$(this).removeClass('hovered');
		})
		
		/*MENU MORE*/
		$('.navmenu_more > a').click(function(){
			var hiddenList = $(this).parent().find('ul');
			hiddenList.slideToggle();
			$(this).parent().toggleClass('opened');
			return false;
		})
		
		
		/*INDEX TABS*/
		$('.indextabs span').click(function(){
			if(!$(this).hasClass('active')) {
				var thisId = $(this).attr('id');
				$(this).toggleClass('active').siblings().removeClass('active');
				$('#'+thisId+'_tab').show().siblings('div.indextab').hide();
				if(thisId == 'rusObrTab') {
					$(this).closest('.title-block').addClass('orange');
				} else {
					$(this).closest('.title-block').removeClass('orange');
				}
			}
		})
		$('#rusObrTab_tab .rusObr-list-one').hover(function(){
			var width = $(this).width(),
				height = $(this).height();
			
			$(this).addClass('rusHovered').css({'width':width,'height':height});
			
		},function(){
			$(this).removeClass('rusHovered').css({'width':'auto','height':'auto'});
			$(this).parent().css({'height':'auto'});
		})
		
		$('.obrazy .rusObr-list-one-wrap').hover(function(){
			var width = $(this).width(),
				height = $(this).height(),
				liMas  = $(this).parents('ul').find('li'),
				thisli = $(this).closest('li');

			$(this).addClass('rusHovered').css({'width':width*2-1,'height':height});
			$(this).find('.rusObr-list-one').css({'width':width - 51,'height':height - 50});
			$(this).find('.rusHovered-contacts').css({'width':width -54,'height':height - 50 });
			if (liMas.index(thisli)%4 == 3) {
				$(this).find('.rusHovered-contacts').css({'left':0});
				$(this).find('.rusObr-list-one').css({'left':width});
				$(this).css({'left':-width});
			} else {
				$(this).find('.rusHovered-contacts').css({'left':width});
			};
			
		},function(){
			$(this).removeClass('rusHovered').css({'width':'auto','height':'auto','left':0});
			$(this).find('.rusObr-list-one').css({'width':'auto','height':'auto','left':0});
			$(this).find('.rusHovered-contacts').css({'width':'auto','height':'auto'});
			$(this).parent().css({'height':'auto'});
		})
		
		
		
		$('#video_sort li').click(function(){
			$(this).addClass('active').siblings().removeClass('active');
		})
		
		
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
		
		
		/*video filter*/
		$('.video-filter label, .checks-filter label').click(function(e){
			var targetName  = e.target.nodeName.toLowerCase();
			if (targetName == 'span') {
				$(this).closest('label').toggleClass('checked');
			}
		})
		
		$('.video-filter .check_all, .checks-filter .check_all').click(function(){
			var parentDiv = $(this).closest('.simple-filter-block');
			
			if (!$(this).data('checked')) {
				parentDiv.find('input:checkbox').attr('checked','checked');
				parentDiv.find('label').addClass('checked');
				$(this).data('checked', true).addClass('checked');
				
			} else {
				parentDiv.find('input:checkbox').removeAttr('checked');
				parentDiv.find('label').removeClass('checked');
				$(this).data('checked', false).removeClass('checked');
				
			}
		})
		
		
		$('#auth-link').click(function(){
			$(this).next('.top_2_reg').slideToggle();
			return false;
		})
		
		
	});
	
	
	

