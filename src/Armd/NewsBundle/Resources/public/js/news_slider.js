$(function() {
    var armdSlider = {
        thumbs: $('#thumbscarousel'),
        carousel: $('#carousel'),
        thumbsParams: {
            circular: false,
            auto: false,
            width: 100,
            height: 50,
            scroll: {
                duration: 200
            },
            items: {
                visible: 1,
                width: 100,
                height: 50
            }
        },
        carouselParams: {
            items: 1,
            scroll: {
                fx: 'crossfade'
            },
            auto: {
                timeoutDuration: 5000,
                duration: 2000
            },
            pagination: {
                container: '#pager',
                duration: 300
            }
        },
        delay: function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        },
        slidesResize:function(){
            wrapperWidth = $('#news-slider-wrapper').width();
            wrapperHeight = parseInt(wrapperWidth/2, 10);
            
            $('#news-slider-wrapper').css({'width': wrapperWidth, 'height':wrapperHeight});
            $('#carousel img').css({'width': wrapperWidth, 'height':'auto'});
            $('#carousel a').css({'width': wrapperWidth, 'height':wrapperHeight});
        },
        init: function() {
            armdSlider.slidesResize();
            armdSlider.slidersInit();
            
            $(window).resize(function() {
                $('#news-slider-wrapper').css({'visibility':'hidden', width:'100%'});
                if(this.resizeTO) clearTimeout(this.resizeTO);
                this.resizeTO = setTimeout(function() {
                    $(this).trigger('resizeEnd');
                }, 500);
            });
            
            $(window).bind('resizeEnd', function(){
                var wrapperWidth, wrapperHeight;
                armdSlider.slidersDestroy();
                armdSlider.slidesResize();
                
                setTimeout(function(){
                    armdSlider.slidersInit();
                    $('#news-slider-wrapper').css('visibility', 'visible');
                }, 100);
            })
        },
        slidersDestroy: function() {
            $('#pager').unbind('hover');
            $('#pager a').unbind('mouseenter');
            
            armdSlider.carousel.trigger('destroy', true);
            armdSlider.thumbs.trigger('destroy', true);
            $('#carousel').attr('style', '');
            $('#thumbscarousel').attr('style', '');
        },
        slidersInit: function() {
            armdSlider.carousel.carouFredSel(armdSlider.carouselParams);
            armdSlider.thumbs.carouFredSel(armdSlider.thumbsParams);
            $('#pager').hover(function() {
                var current = $('#carousel').triggerHandler( 'currentPosition' );
                armdSlider.thumbs.trigger( 'slideTo', [ current, 0, true, { fx: 'none' } ] );
                $('#thumbs').stop().fadeTo(300, 1);
            }, function() {
                $('#thumbs').stop().fadeTo(300, 0);
            });

            $('#pager a').mouseenter(function() {
                var index = $('#pager a').index( $(this) );
                //	clear the queue
                armdSlider.thumbs.trigger( 'queue', [[]] );
                //	scroll
                armdSlider.thumbs.trigger( 'slideTo', [index, { queue: true }] );
            });
            
        }
        
    }
    
   armdSlider.init();

    
    
    
    
});