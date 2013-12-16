jQuery.fn.cleardefault = function(){
    return this.focus(function(){
        if (this.value == this.defaultValue) {
            this.value = "";
        }
    }).blur(function(){
        if (!this.value.length) {
            this.value = this.defaultValue;
        }
    });
};

$(function(){
    $("input[type=text]").cleardefault();
});

$(window).load(function(){
    //Mainpage featured news widget
    $('.js-simple-tabs').each(function(){
        var nav = $(this).find('.js-tabs-nav a'),
            tabs = $(this).find('.js-tabs-list').children();

        tabs.hide();
        tabs.filter(nav.filter('.current').attr('href')).show();

        nav.on('click', function(){
            tabs.hide();
            nav.removeClass('current');
            $(this).addClass('current');
            tabs.filter($(this).attr('href')).show();

            return false;
        });
    });

    //Mainpage "Movies" widget
    $('#cinema_block').find('.order').click(function(e){
        e.preventDefault();
        $('#cinema_block').find('.order').removeClass('current');
        $(this).addClass('current');
        $('#cinema_widget').load($(this).attr('href'));
    });

    //Mainpage "Virtual theater" widget
    $('#theater_block').find('.order').click(function(e){
        var theaterWidget = $('#theater_widget');

        e.preventDefault();

        $('#theater_block').find('.order').removeClass('current');
        $(this).addClass('current');

        theaterWidget.load($(this).attr('href'), function(){
            theaterWidget.parent().css({
                'height': $(this).children(':first').height(),
                'overflow': 'hidden'
            });
            theaterWidget.caroufredsel({
                items: 1,
                visible: 1,
                width: 'auto',
                align: 'center',
                auto: false,
                pagination: {
                    container: $('#theater_block').find('.b-pagination-nav ul'),
                    anchorBuilder: function(nr){
                        return '<li><a href="#'+nr+'">'+nr+'</a></li>';
                    }
                },
                onCreate: function(data){
                    $('#theater_block').addClass('js-fred-active');

                    data.items.each(function(){
                        $(this).addClass('visible');
                    });

                    theaterWidget.trigger('slideTo', 0);
                },
                scroll: {
                    fx: "fade"
                }
            });
        });
    });

    //Mainpage "Lectures" widget
    $('#lecture_block').find('.order').click(function(e){
        e.preventDefault();

        $('#lecture_block').find('.order').removeClass('current');
        $(this).addClass('current');
        $('#lecture_widget').load($(this).attr('href'));
    });
});