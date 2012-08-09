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
		if ($( "#calendar" ).length > 0) {
			$('.calendar-title').click(function(){
				$(this).next('#calendar').slideToggle();
			})
			
			$.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );
			$( "#calendar" ).datepicker({showOtherMonths: true});
		}
		/*$('.ppa-block').hover(function(){
			$(this).find('.ppa-over').show();
		},
		function(){
			$(this).find('.ppa-over').hide();
		});*/
		
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
		/*MENU MORE*/
		$('.navmenu_more > a').click(function(){
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
	});
	
	
	

