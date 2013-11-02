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

/* End input clear functionality */
$(function(){
    $("input[type=text]").cleardefault();

//    /* Load more functionality */
//
//    var loadMore = function () {
//        if(!$('.b-post-list').size()){
//            return false;
//        }
//        $.ajax({
//            url: 'blog-list.html',
//            success: function (data) {
//                var $data = $(data).find('.b-post-list article').css({'opacity': 0}).addClass('new-item');
//                // Replace div#the-posts with the name of your post container
//                $('.b-post-list').append($data);
//                $('.b-post-list').find('.new-item').each(function(){
//                    var $self = $(this);
//                    $self.animate({
//                        opacity: 1
//                    },{
//                        queue: false,
//                        complete: function(){
//                            $(this).removeAttr('style').removeAttr('class');
//                        }
//                    }, 500);
//                });
//            }
//        }); // End Ajax
//        return false;
//    }; // End loadMore
//
//    $('.load-more-link').on('click', loadMore);
//    /* Load more functionality */

});

$(window).load(function(){
    if ($('.slides').size()){
        $('.slides').each(function(){
            var slider = $(this),
                displayPageNumber = function(slider, current){
                    var navContainer = slider.closest('.b-slider-wrapper"').find('.slider-nav-panel'),
                        totalCol = slider.find('> div').size();

                    navContainer.find('.current-number').text(current);
                    navContainer.find('.total-number').text(totalCol);
                };

            slider.caroufredsel({
                items: 1,
                visible: 1,
                width: 'auto',
                align: 'center',
                auto: false,
                next: slider.siblings('.slider-nav-panel').find('.next-slider-arrow'),
                prev: slider.siblings('.slider-nav-panel').find('.prev-slider-arrow'),
                onCreate: function(data){
                    displayPageNumber($(this), slider.triggerHandler('currentPosition') + 1);
                },
                scroll: {
                    fx: 'fade',
                    onBefore: function(data){
                        displayPageNumber($(this), slider.triggerHandler('currentPosition') + 1);
                    }
                }
            });
        });
    }

    $('.user-favorites-button').on('click', function(e){
        var $this = $(this),
            type = $this.data('type'),
            id = $this.data('id');

        e.preventDefault();

        $.ajax({
            url: Routing.generate('armd_user_favorites_add'),
            type: 'get',
            data: { 'type': type, 'id': id },
            dataType: 'html'
        }).done(function(data){
            if (data == '1') {
                $this.remove();
                alert('Объект добавлен в «Избранное» Вашего профиля.');
            }
        });
    });

    // ikotelov 14.08.2013 iframe video-processing function
//    $('.js-process-video').each(function(){
//        var iframe = $(this).find('iframe'),
//            youid = iframe.attr('src').split('/embed/')[1],
//            thumb = $('<img/>', {
//                'src' : 'http://img.youtube.com/vi/'+youid.split('?')[0]+'/hqdefault.jpg',
//                'width' : iframe.attr('width'),
//                'height' : iframe.attr('height')
//            }).add($('<span class="e-play"></span>)')).on('click', function(){
//                    thumb.remove();
//                    iframe.show();
//                });
//        $(this).prepend(thumb);
//        iframe.hide();
//    });
    // end ikoptelov 14.0.2013

    // ikoptelov 08.08.2013 Small and Simple tabs snippet
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

    // ikoptelov 08.08.2013 Small and Simple tabs snippet
    // var oneImageCarousel = $(".js-oneimage-carousel");

    /*if (oneImageCarousel.size() != 0) {
        oneImageCarousel.find('.js-carousel-container').carouFredSel({
            circular: false,
            infinite: false,
            auto    : false,
            prev    : {
                button  : null
            },
            next    : {
                button  : null
            },
            onCreate : function( data ) {
                data.items.each(function(){
                    $(this).addClass('visible');
                });
            },
            scroll  : {
                onBefore : function( data ) {
                    oneImageCarousel.find('.js-carousel-item').removeClass('visible');
                },
                onAfter : function( data ) {
                    data.items.visible.each(function(){
                        $(this).addClass('visible');
                    });
                }
            },
            pagination  : oneImageCarousel.find('.js-oneimage-carousel-nav')
        });
    }*/
    // end ikoptelov 08.08.2013

    $('#russia_image_block').find('.order').click(function(e){
        e.preventDefault();

        $('#russia_image_block').find('.order').removeClass('current');
        $(this).addClass('current');

        $('#russia_image_widget').load($(this).attr('href'), function(){
            $('#russia_image_widget').caroufredsel({
                items: 1,
                visible: 1,
                width: 'auto',
                align: 'center',
                auto: false,
                pagination: {
                    container: $('#russia_image_block').find('.b-pagination-nav ul'),
                    anchorBuilder: function(nr){
                        return '<li><a href="#'+nr+'">'+nr+'</a></li>';
                    }
                },
                onCreate: function(data){
                    data.items.each(function(){
                        $(this).addClass('visible');
                    });
                    $('#russia_image_widget').trigger('slideTo', 0);
                },
                scroll: {
                    onBefore: function(data){
                        $('#russia_image_widget').children().removeClass('visible');
                    },
                    onAfter: function(data){
                        data.items.visible.each(function(){
                            $(this).addClass('visible');
                        });
                    }
                }
            });
        });
    });

    $('#cinema_block').find('.order').click(function(e){
        e.preventDefault();

        $('#cinema_block').find('.order').removeClass('current');
        $(this).addClass('current');
        $('#cinema_widget').load($(this).attr('href'));
    });

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

    $('#lecture_block').find('.order').click(function(e){
        e.preventDefault();

        $('#lecture_block').find('.order').removeClass('current');
        $(this).addClass('current');
        $('#lecture_widget').load($(this).attr('href'));
    });

    // From old js
    var qtipDefaults = {
        position: {
            my: "top center",
            at: "bottom center"
        },
        show: {
            event: "click",
            effect: function(){
                $(this).slideDown(100);
            },
            solo: true,
            modal: {
                on: true
            }
        },
        hide: {
            event: "unfocus",
            effect: function(){
                $(this).slideUp(100);
            }
        },
        style: {
            classes: "qtip-light qtip-shadow",
            tip: {
                corner: true,
                width: 24,
                height: 12
            }
        }
    };

    var loginPopup = $("a[href='#login-popup']");

    if (loginPopup.length > 0) {
        loginPopup.qtip($.extend(true, qtipDefaults, {
            content: $("#login-popup")
        })).click(function(){
            return false;
        });
    }

    var appstorePopup = $("a[href='#appstore-popup']");

    if (appstorePopup.length > 0) {
        appstorePopup.qtip($.extend(true, qtipDefaults, {
            content: $("#appstore-popup").html(),
            position: {
                my: "top right"
            },
            style: {
                tip: {
                    mimic: "top center",
                    offset: 30
                }
            }
        })).click(function(){
            return false;
        });
    }
});