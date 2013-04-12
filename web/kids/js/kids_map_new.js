
$(function(){
			
    $('#mos').data('clicked', true);
    
    $('.point').hover( function(){
        $(this).addClass('active');
        
    }, function(){
        if( !$(this).data('clicked')) {
            $(this).removeClass('active');
        }
    })
    
    
    
    $('.map-point').on('click', $(this), function(){
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
            //window.location = '#window';			
        }
        
        
        
        
    })
    
    /*FEEDBACK*/
    
    jqOLD("textarea#message").cuText({
        showArrows: false
    });
    
    $('#submit-btn').on('click', $(this), function(){
        if ($(this).hasClass('.submit-btn-inactive')) {
            return false;
        }    
    })
    
    $('#feedback-popup').on('keyup', $('input, textarea'), function(){
        var empty = false;
        $('input[required],textarea[required]').each(function(){
            if ($(this).val() == '') {
                empty = true;
                return;
            }
        })
        
        if (!empty) {
            $('#submit-btn').removeClass('submit-btn-inactive');
        } else {
            $('#submit-btn').addClass('submit-btn-inactive');
        }
        
    })
    
    jqOLD("a.modal").fancybox({
        padding: 15,
        autoSize: false,
        width: "965",
        height: "584",
        fitToView: false
    });
    
    $('.fancy-hide').css('visibility','visible').hide();
    
    
})