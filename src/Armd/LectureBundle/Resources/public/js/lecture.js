$(function(){
    mkLecture.init();
});

var mkLecture = {

    init: function() {
		/*video filter*/
		$('.video-filter label, .checks-filter label').click(function(e){
			var targetName  = e.target.nodeName.toLowerCase();
			if (targetName == 'span') {
				$(this).closest('label').toggleClass('checked');
			}
		});

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
		});

    }
};