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
	
	
	

