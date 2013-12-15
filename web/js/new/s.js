$(function() {
   /* Chrome/safari detect */
   var ua = navigator.userAgent.toLowerCase();

   if ((ua.indexOf('safari')!=-1)) {

       if(ua.indexOf('chrome')  > -1) {
           $("body").addClass('m-chrome');
       } else {
           $("body").addClass('m-safari');
       }
   }
   if((ua.indexOf('firefox')!=-1)) {
       $("body").addClass('m-firefox');
   }
   /* Chrome/safari detect eof */
    if( $.browser.opera ){
        $("body").addClass('m-opera');
    }

    // ikoptelov 14.08
    $('.m-sights-carousel .b-main-slides, .m-specialproject .b-main-slides, .b-slider-wrapper .slides').each(function(){
        $(this).css({
            'height' : $(this).children(':first').height(),
            'overflow' : 'hidden'
        });
    });
});
$(window).load(function() {
    /* Sign and museums main carousel */
    $('.m-sights-carousel .b-main-slides').each(function(){
        var slider = $(this),
            paginationContainer = $('<div />', {'class': 'b-pagination-nav'}),
            paginationUl = $('<ul />'),
            conatiner = slider.closest('.b-simple-slider');
        conatiner.append(paginationContainer);
        paginationUl.appendTo(conatiner.find('.b-pagination-nav'));
        slider.caroufredsel({
            items: 1,
            visible: 1,
            width: 'auto',
            align: 'center',
            auto: false,
            pagination: {
                container: conatiner.find('.b-pagination-nav ul'),
                anchorBuilder: function(nr){
                    return '<li><a href="#'+nr+'">'+nr+'</a></li>';
                }
            },
            onCreate : function( data ) {
                conatiner.addClass('js-fred-active');
                data.items.each(function(){
                    $(this).addClass('visible');
                });
            },
            scroll  : {
                onBefore : function( data ) {
                    slider.children().removeClass('visible');
                },
                onAfter : function( data ) {
                    data.items.visible.each(function(){
                        $(this).addClass('visible');
                    });
                }
            }
        });
    });
    /* End sign carousel */
    /* Special projects carousel */
    $('.theater-carousel .b-main-slides, .m-specialproject .b-main-slides').each(function(){
        var slider = $(this),
            paginationContainer = $('<div />', {'class': 'b-pagination-nav'}),
            paginationUl = $('<ul />'),
            conatiner = slider.closest('.b-simple-slider');
        conatiner.append(paginationContainer);
        paginationUl.appendTo(conatiner.find('.b-pagination-nav'));
        slider.caroufredsel({
            items: 1,
            visible: 1,
            width: 'auto',
            align: 'center',
            auto: false,
            pagination: {
                container: conatiner.find('.b-pagination-nav ul'),
                anchorBuilder: function(nr){
                    return '<li><a href="#'+nr+'">'+nr+'</a></li>';
                }
            },
            onCreate : function( data ) {
                conatiner.addClass('js-fred-active');
                data.items.each(function(){
                    $(this).addClass('visible');
                });
            },
            scroll  : {
                fx: 'fade'
            }
        });
    });
    /* Special projects carousel */
});