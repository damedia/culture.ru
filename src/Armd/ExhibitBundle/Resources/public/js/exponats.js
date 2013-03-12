var exhibit = {
    scrollPane: null,
    api: null,
    loading: false,
    offset: 0,
    stopLoad: false,
    addElOneHover: function() {
        $('.el-one').hover(function() {
            $(this).addClass('el-one-hovered');
        }, function() {
            $(this).removeClass('el-one-hovered');
        });
    },
    init: function(data) {
        //$(window).load(function() {
           //alert("window load occurred!");
        //});
        $(function() {
            exhibit.setExhibits(data);
        });
        $(window).load(function() {
            //exhibit.setExhibits(data);
            exhibit.scrollPane = $('.exponats-scroll-pane').jScrollPane({animateScroll: true});
            exhibit.api = exhibit.scrollPane.data('jsp');

            var throttleTimeout;
            $(window).bind(
                    'resize',
                    function()
                    {
                        if ($.browser.msie) {
                            if (!throttleTimeout) {
                                throttleTimeout = setTimeout(
                                        function()
                                        {
                                            exhibit.api.reinitialise();
                                            throttleTimeout = null;
                                        },
                                        50
                                        );
                            }
                        } else {
                            exhibit.api.reinitialise();
                        }
                    }
            );

            $('#scroll-right').bind(
                    'click',
                    function()
                    {
                        exhibit.api.scrollByX(200);
                    }
            );
            $('#scroll-left').bind(
                    'click',
                    function()
                    {
                        exhibit.api.scrollByX(-200);
                    }
            );

            exhibit.scrollPane
                    .bind(
                    'mousewheel',
                    function(event, delta, deltaX, deltaY)
                    {
                        exhibit.api.scrollByX(delta * -100);
                        return false;
                    }
            )
                    .bind(
                    'jsp-scroll-x',
                    function(event, scrollPositionX, isAtLeft, isAtRight)
                    {
                        /*
                         console.log('Handle jsp-scroll-x', this,
                         'scrollPositionX=', scrollPositionX,
                         'isAtLeft=', isAtLeft,
                         'isAtRight=', isAtRight);
                         */
                        if (isAtLeft) {
                            $('#scroll-left').hide();
                        } else if (isAtRight) {
                            $('#scroll-right').hide();
                            exhibit.loadExhibits();
                        } else {
                            $('#scroll-left').show();
                            $('#scroll-right').show();
                        }
                    }
            )

            $('#carousel img.active').click();

            $('#exp-types').on('click', 'a', function() {
                if (!$(this).parent().hasClass('active')) {
                    var type = $(this).attr('href').substr(1);

                    exhibit.replaceExhibits(type);

                    $(this).parent().addClass('active').siblings().removeClass();
                    $('#exponats-content').removeClass().addClass(type);
                    exhibit.api.scrollToX(0, false);
                    exhibit.api.reinitialise();
                    exhibit.addElOneHover();
                }

                return false;
            });

            $('#exp-categories').on('click', '.with-filter a', function() {
                var li = $(this).parent();
                console.log(li.hasClass('filtered'));
                if (li.hasClass('filtered')) {
                    li.removeClass('filtered');
                    $('#exp-filter').slideUp();
                } else {
                    li.addClass('filtered').siblings().removeClass('filtered');
                    $('#exp-filter').show();
                }


                return false;
            });

            $('#exp-filter').on('click', '#filter-handle', function() {
                $('#exp-categories li').removeClass('filtered');
                $('#exp-filter').slideUp();
            });

            $('.alphabet').on('click', 'a', function() {
                $(this).toggleClass('active').siblings().removeClass('active');
                return false;
            });

            $('#exponats-tags').on('click', '.delete', function() {
                var tag = $(this).prev(),
                        tagText = tag.text();
                $(this).parent().remove();
                $('.fiter-results a').filter(function(index) {
                    return $(this).text() === tagText;
                }).removeClass('active');
            });

            $('#exp-filter .fiter-results').on('click', 'a', function() {
                var tag = $(this),
                        tagText = tag.text(),
                        newTag;

                if (tag.hasClass('active')) {
                    $(this).removeClass('active');
                    $('#exponats-tags a').filter(function(index) {
                        return $(this).text() === tagText;
                    }).parent().remove();
                } else {
                    newTag = $('<li><a href="#">' + tagText + '</a><span class="delete"></span></li>');
                    newTag.appendTo('#exponats-tags');
                    $(this).addClass('active');
                }

                return false;
            });

            //$('#carousel').jcarousel({});
            /*
            $(".jcarousel-container")
                    .mousewheel(function(event, delta) {
                if (delta < 0)
                    $(".jcarousel-next").click();
                else if (delta > 0)
                    $(".jcarousel-prev").click();
                return false;
            });


            $('#carousel').on('click', 'img', function() {

                var borderWidth = $(this).width() - 4,
                        borderHeight = $(this).height() - 4,
                        imgBorder = $('<i style="width:' + 36 + 'px; height:' + 48 + 'px; "></i>'),
                        fullImageSrc = $(this).attr('data-fullimage'),
                        fullImageWidth, fullImageHeight;

                $(this).parent().append(imgBorder)
                        .siblings().find('i').remove();

                $(this).addClass('active')
                        .parent().siblings().find('img').removeClass('active');

                $('.main-zoomed-image img').css('opacity', 0).attr('src', fullImageSrc);
                fullImageWidth = $('.main-zoomed-image img').width();
                fullImageHeight = $('.main-zoomed-image img').height();

                $('.main-zoomed-image img').css({
                    'opacity': 1,
                    'marginLeft': -fullImageWidth / 2,
                    'marginTop': -fullImageHeight / 2
                });
            });

            var image, imageWidth, imagePos, popupImage, imgPosLeft, imgPosTop, popupTimeout;

            $('#carousel').on('mouseenter', 'li', function() {
                image = $(this).find('img');
                imagePos = image.offset();
                imgPosLeft = imagePos.left;
                imgPosTop = imagePos.top - 90;
                imageWidth = image.width();
                popupImage = $('<img src="' + image.attr('src') + '" alt="" class="popup-image" style="opacity:0;top:' + imgPosTop + 'px; left:' + imgPosLeft + 'px" id="image_' + image.index() + '" />');
                $('.popup-image').remove();
                $('body').append(popupImage);
                imgPosLeft = imgPosLeft + 20 - popupImage.width() / 2;
                popupImage.css({'left': imgPosLeft, 'opacity': '1'});

            });

            $('#carousel').on('mouseout', 'li', function() {
                $('.popup-image').remove();
            });


            $('#details-btn').on('click', function() {
                if (!$(this).data('opened')) {
                    $('#exponats-details').show();
                    $('#exponat-main-container').hide();
                    $(this).data('opened', true);
                    $(this).text('Спрятать детали');
                } else {
                    $('#exponats-details').hide();
                    $('#exponat-main-container').show();
                    $(this).data('opened', false);
                    $(this).text('Детали');
                }
                return false;
            });
            */
        });
    },
    replaceExhibits: function (type) {
        var i = 0,
            lines = $('.exponats-scroll .line > div').remove();

        while (true) {
            if (!lines.eq(i).size()) {
                break;
            }

            $('.exponats-scroll .first-line').append(lines.eq(i));
            i++;

            if (lines.eq(i).size() && (type == 'twolines' || type == 'threelines')) {
                $('.exponats-scroll .second-line').append(lines.eq(i));
                i++;
            }

            if (lines.eq(i).size() && type == 'threelines') {
                $('.exponats-scroll .third-line').append(lines.eq(i));
                i++;
            }
        }
    },
    setExhibits: function(data) {
        if (!data['data'].length) {
            exhibit.stopLoad = true;
            
            return false;
        }
        
        var elAppend, w, width = {},
                el = $('#el-one-blank').eq(0),
                type = $('#exp-types li.active a').attr('href').substr(1);
        
        width['first'] = 0; 
        width['second'] = 0; 
        width['third'] = 0;
        
        $('.exponats-scroll .first-line > div').each(function () {
            width['first'] += $(this).width();
        });
        $('.exponats-scroll .second-line > div').each(function () {
            width['second'] += $(this).width();
        });
        $('.exponats-scroll .third-line > div').each(function () {
            width['third'] += $(this).width();
        });
        console.log(width);
        exhibit.offset = data['offset'];

        for (i in data['data']) {
            if (type == 'threelines') {
                if (width['first'] <= width['second'] && width['first'] <= width['third']) {
                    elAppend = $('.exponats-scroll .first-line');
                    w = 'first';
                } else if (width['second'] <= width['third']) {
                    elAppend = $('.exponats-scroll .second-line');
                    w = 'second';
                } else {
                    elAppend = $('.exponats-scroll .third-line');
                    w = 'third';
                }
            } else if ((type == 'twolines' && width['first'] <= width['second']) || type == 'oneline') {
                elAppend = $('.exponats-scroll .first-line');
                w = 'first';
            } else {
                elAppend = $('.exponats-scroll .second-line');
                w = 'second';
            }

            el = el.clone();           
            el.attr('style', '');
            el.find('a img').attr('src', data['data'][i]['img']);            
            elAppend.append(el);
            width[w] += el.width();
        }

        exhibit.addElOneHover();
    },
    loadExhibits: function() {
        if (exhibit.loading || exhibit.stopLoad) {
            return false;
        }

        exhibit.loading = true;
        armdMk.startLoadingBlock();
        $.ajax({
            url: Routing.generate('armd_load_exhibits', {'offset': exhibit.offset}),
            type: 'post',
            data: {
            },
            dataType: 'json',
            success: function(data) {
                exhibit.setExhibits(data);
                
                if (!exhibit.stopLoad) {                   
                    setTimeout(function () { exhibit.api.reinitialise(); }, 300);
                }           
            },
            complete: function() {
                exhibit.loading = false;
                armdMk.stopLoadingBlock();
            }
        });
    }
};