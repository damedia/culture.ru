var exhibit = {
    scrollPane: null,
    api: null,
    loading: false,
    offset: 0,
    stopLoad: false,
    data: [],
    addElOneHover: function() {
        $('.el-one').hover(function() {
            $(this).addClass('el-one-hovered');
        }, function() {
            $(this).removeClass('el-one-hovered');
        });
    },
    init: function(data) {
        exhibit.data = data;
        //$(window).load(function() {
           
        //});
        $(function() {
            exhibit.setExhibits(exhibit.data);
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

                    $(this).parent().addClass('active').siblings().removeClass();
                    exhibit.replaceExhibits();
                    $('#exponats-content').removeClass().addClass(type);
                    exhibit.api.scrollToX(0, false);
                    exhibit.api.reinitialise();
                    exhibit.addElOneHover();
                }

                return false;
            });

            $('#exp-categories').on('click', '.with-filter a', function() {
                var that = this, li = $(this).parent();
                console.log(li.hasClass('filtered'));
                if (li.hasClass('filtered')) {
                    li.removeClass('filtered');
                    $('#' + $(this).attr('data-id')).slideUp();
                } else {
                    li.addClass('filtered').siblings().removeClass('filtered');
                    $('.exp-filter').slideUp();
                    $('#' + $(that).attr('data-id')).show();
                }


                return false;
            });

            $('.exp-filter').on('click', '#filter-handle', function() {
                $('#exp-categories li').removeClass('filtered');
                $(this).parent().slideUp();
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

            $('.exp-filter .fiter-results').on('click', 'a', function() {
                var tag = $(this),
                        tagText = tag.text(),
                        newTag, cl = '';

                if (tag.hasClass('active')) {
                    $(this).removeClass('active');
                    $('#exponats-tags a').filter(function(index) {
                        return $(this).text() === tagText;
                    }).parent().remove();
                } else {
                    if ($(this).attr('data-fname') == 'museum') {
                        cl = 'big-tag';
                    }
                    
                    newTag = $('<li data-fname="' + $(this).attr('data-fname') + '" class="' + cl + '"><a href="#">' + tagText + '</a><span class="delete"></span></li>');
                    
                    if ($('#exponats-tags li[data-fname="' + $(this).attr('data-fname') + '"]').size()) {
                        $('#exponats-tags li[data-fname="' + $(this).attr('data-fname') + '"]:last').after(newTag);
                    } else {
                        if ($(this).attr('data-fname') == 'museum') {
                            $('#exponats-tags').prepend(newTag);
                        } else {
                            $('#exponats-tags').append(newTag);
                        }                      
                    }
                                      
                    $(this).addClass('active');
                }

                return false;
            });           
        });
    },
    replaceExhibits: function () {
        $('.exponats-scroll .line > div').remove();
        exhibit.setExhibits(exhibit.data);
    },
    setExhibitsTest: function(data) {
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
        
        $('.exponats-scroll .first-line > div > a img').each(function () {
            width['first'] += $(this).width();
        });
        $('.exponats-scroll .second-line > div > a img').each(function () {
            width['second'] += $(this).width();
        });
        $('.exponats-scroll .third-line > div > a img').each(function () {
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
            width[w] += el.find('> a img').width();
            el.find('> a img').load(function() {
                console.log('width - ' + $(this).width())
            });
            console.log(width);
        }

        exhibit.addElOneHover();
    },
    setExhibits: function(data) {
        if (!data['data'].length) {
            exhibit.stopLoad = true;
            
            return false;
        }
        
        var elAppend, w, el, width = {},
                elClone = $('#el-one-blank').eq(0),
                type = $('#exp-types li.active a').attr('href').substr(1);
                      
        width['first'] = $('.exponats-scroll .first-line > div').size();
        width['second'] = $('.exponats-scroll .second-line > div').size();
        width['third'] = $('.exponats-scroll .third-line > div').size();       
        
        exhibit.offset = data['offset'];

        for (var i in data['data']) {
            if (type == 'threelines') {
                if (width['first'] <= width['second'] && width['first'] <= width['third']) {
                    elAppend = $('.exponats-scroll .first-line');
                    width['first']++;
                } else if (width['second'] <= width['third']) {
                    elAppend = $('.exponats-scroll .second-line');
                    width['second']++;
                } else {
                    elAppend = $('.exponats-scroll .third-line');
                    width['third']++;
                }
            } else if ((type == 'twolines' && width['first'] <= width['second']) || type == 'oneline') {
                elAppend = $('.exponats-scroll .first-line');
                width['first']++;
            } else {
                elAppend = $('.exponats-scroll .second-line');
                width['second']++;
            }

            el = elClone.clone();           
            el.attr('style', '');
            el.find('a img').attr('src', data['data'][i]['img']);  
            el.find('.el-one-title').text(data['data'][i]['title']);
            el.find('.el-one-year').text(data['data'][i]['date']);
            el.find('.el-one-museum').text(data['data'][i]['museum']['title']);
            
            for (var a in data['data'][i]['authors']) {              
                el.find('.el-one-authors').append('<p><a href="">' + data['data'][i]['authors'][a]['title'] + '</a></p>');
            }
            
            elAppend.append(el);    
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
                exhibit.data.data = $.merge(exhibit.data.data, data.data);
                exhibit.data.offset = data.offset;               
                exhibit.setExhibits(data);
                
                if (!exhibit.stopLoad) {                   
                    setTimeout(function () { exhibit.api.reinitialise(); }, 200);
                }           
            },
            complete: function() {
                exhibit.loading = false;
                armdMk.stopLoadingBlock();
            }
        });
    }
};