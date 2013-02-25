        	$(document).ready(function(){
        		
        		var slides_params = {
        			'generateNextPrev' : true,
        			'generatePagination' : false,
        			'slidesLoaded' : function() {
        				hidePrev('main_quiz');
        				$('#main_quiz .next').addClass('first_slide').text('Начнем?');
        				
        			},
        			'animationComplete' : function() {
        				
        				var listItem,
        					index;
        					
        				hidePrev('main_quiz');	
        				
        				$('#main_quiz .slide').each(function(){
        					if ($(this).css('display') == 'block') 
        						listItem = $(this);
        				});
        				
        				index = $('#main_quiz .slide').index(listItem);
        				
        				if (index > 0) {
        					$('#main_quiz .next').removeClass('first_slide').text('Дальше');
        				}
        				else {
        					showNext('petergof_quiz');
        					$('#main_quiz .next').addClass('first_slide').text('Начнем?');
        				}
        			},
        			'animationStart' : function() {
        				hideNext('main_quiz');
        			}
        		};
        		
        		$('#main_quiz').slides(slides_params);
        		
        		
     		   	$('#main_quiz .answers a').live('click', function(){
     		   		
     		   		$('#main_quiz .answers a').removeClass('active');
     		   		var cl = $(this).parent('li').attr('class');
     		   		$(this).addClass('active');
     		   		
     		   		$(this).parents('div.answers').attr('class', 'answers');
     		   		$(this).parents('div.answers').addClass(cl);
     		   		
     		   		if ($(this).attr('valid') == 'true')
     		   			showNext('main_quiz');
     		   		else 
     		   			hideNext('main_quiz');
     		   		return false;
     		   	});
                
                
                $('.get-diplom').click(function(){
                    var thisSlide = $(this).closest('.slide'),
                        thisName = $(this).prev('input').val();
                    if($.trim(thisName) !='') {
                        thisSlide.hide();
                        thisSlide.next().find('.winner-name').html(thisName);    
                        thisSlide.next().show();
                        Cufon.replace('.winner-name');
                    } else {
                        alert('Введите имя');
                    }
                     return false;
                    
                })
                
        		
        	});
        	
        	function hideNext(context) {
        		$('#' + context + ' .next').hide();
        	}
        	function hidePrev(context) {
        		$('#' + context + ' .prev').hide();
        	}        		
        	function showNext(context) {
        		$('#' + context + ' .next').show();
        	}
        	function showPrev(context) {
        		$('#' + context + ' .prev').show();
        	}     