$(function(){
			
			$('#mos').data('clicked', true);
			
			$('.point').hover( function(){
				$(this).addClass('active');
				
			}, function(){
				if( !$(this).data('clicked')) {
					$(this).removeClass('active');
				}
			})
			
			
			
			$('.point').on('click', $(this), function(){
				var point = $(this).attr('id'),
					pointName = $('.lenta-wrap', $(this)).html(),
					forSlider = $('.for-slider', $(this)).html();
				
				$(this).siblings().data('clicked', false);
				
				if($(this).data('clicked')) {
					$(this).data('clicked', false);
					$(this).removeClass('active');	
					$('#window').find('.lenta-big-wrap').html('').end()
								.find('.object-slider-content').html('').end()
								.removeClass()
								.hide();
				} else {
					$(this).data('clicked', true);
					$(this).addClass('active').siblings('.point').removeClass('active');	
					$('#window').find('.lenta-big-wrap').html(pointName).end()
								.find('.object-slider-content').html(forSlider).end()
								.removeClass().addClass(point+'_window')
								.show();
					window.location = '#window';			
				}
				
				
				
				
			})
			
		})