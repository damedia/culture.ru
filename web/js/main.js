$(function(){
	$(window).load(function() {
	  $('.flexslider').flexslider({
		animation: "slide",
		controlNav: false,
		slideshow: false
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
	
	$('#category-chooser').on('click', 'a', function(){
		var id = $(this).attr('href');
		$(this).addClass('active').siblings().removeClass('active');
		$(id).show().siblings('.tab').hide();
		
		return false;
	})
	
})
