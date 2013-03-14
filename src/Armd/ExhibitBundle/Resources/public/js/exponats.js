var exhibit = {
    scrollPane: null,
    api: null,
    loading: false,
    offset: 0,
    stopLoad: false,
    objects: [],
    filters: {},
    activeFilters: {},
    tagsDelTimeout: null,
    addElOneHover: function() {
        $('.el-one').hover(function() {
            $(this).addClass('el-one-hovered');
        }, function() {
            $(this).removeClass('el-one-hovered');
        });
    },
    init: function(data, filters) {
        exhibit.objects = data.objects;
        exhibit.offset = data.offset;
        exhibit.filters = filters;
        //$(window).load(function() {
           
        //});
        $(function() {
            exhibit.setExhibits(exhibit.objects);
            exhibit.setFilters();
        });
        $(window).load(function() {
            //exhibit.setExhibits(data);
            exhibit.scrollPane = $('.exponats-scroll-pane').jScrollPane({animateScroll: true});
            exhibit.api = exhibit.scrollPane.data('jsp');

            var throttleTimeout;
            $(window).bind('resize', function() {
                if ($.browser.msie) {
                    if (!throttleTimeout) {
                        throttleTimeout = setTimeout(function() {
                                    exhibit.api.reinitialise();
                                    throttleTimeout = null;
                        }, 
                        50
                        );
                    }
                } else {
                    exhibit.api.reinitialise();
                }
            });
            $('#scroll-right').bind('click', function() {
                exhibit.api.scrollByX(200);
            });
            $('#scroll-left').bind('click', function() {
                exhibit.api.scrollByX(-200);
            });
            exhibit.scrollPane
                .bind('mousewheel', function(event, delta, deltaX, deltaY) {
                    exhibit.api.scrollByX(delta * -100);
                    return false;
                })
                .bind('jsp-scroll-x', function(event, scrollPositionX, isAtLeft, isAtRight) {                   
                    if (isAtLeft) {
                        $('#scroll-left').hide();
                    } else if (isAtRight) {
                        $('#scroll-right').hide();
                        exhibit.loadExhibits();
                    } else {
                        $('#scroll-left').show();
                        $('#scroll-right').show();
                    }
                });

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
                var li = $(this).parent();

                if (li.hasClass('filtered')) {
                    li.removeClass('filtered');
                    $('#' + $(this).attr('data-id')).slideUp();
                    exhibit.filterLoadExhibits();
                } else {
                    li.addClass('filtered').siblings().removeClass('filtered');
                    $('.exp-filter').slideUp();
                    $('#' + $(this).attr('data-id')).show();
                }

                return false;
            });

            $('.exp-filter').on('click', '#filter-handle', function() {
                $('#exp-categories li').removeClass('filtered');
                $(this).parent().slideUp();
                exhibit.filterLoadExhibits();
            });

            $('.alphabet').on('click', 'a', function() {
                $(this).toggleClass('active').siblings().removeClass('active');
                exhibit.setFilterResults(
                    $(this).parent().parent().attr('data-fid'),
                    $(this).text()
                );
                
                return false;
            });

            $('#exponats-tags').on('click', '.delete', function() {
                clearTimeout(exhibit.tagsDelTimeout);
                var tag = $(this).prev(),
                    tagText = tag.text();
                    
                $(this).parent().remove();
                $('.fiter-results a').filter(function(index) {
                    return $(this).text() === tagText;
                }).removeClass('active');
                
                exhibit.setActiveFilter($(this).parent().attr('data-fid'), $(this).parent().attr('data-fitemid'), undefined);               
                exhibit.tagsDelTimeout = setTimeout(function() { exhibit.filterLoadExhibits(); }, 2000);
            });

            $('.exp-filter .fiter-results').on('click', 'a', function() {
                var tag = $(this),
                    tagText = tag.text(),
                    newTag, cl = '', 
                    fId = $(this).attr('data-fid'), 
                    fItemId = $(this).attr('data-fitemid');

                if (tag.hasClass('active')) {
                    $(this).removeClass('active');
                    exhibit.setActiveFilter(fId, fItemId, undefined);
                    $('#exponats-tags a').filter(function(index) {
                        return $(this).text() === tagText;
                    }).parent().remove();
                } else {
                    if (fId == 'museum') {
                        cl = 'big-tag';
                    }
                    
                    newTag = $('<li data-fid="' + fId + '" data-fitemid="' + fItemId + '" class="' + cl + '"><a href="#">' + tagText + '</a><span class="delete"></span></li>');
                    
                    if ($('#exponats-tags li[data-fid="' + fId + '"]').size()) {
                        $('#exponats-tags li[data-fid="' + fId + '"]:last').after(newTag);
                    } else {
                        if (fId == 'museum') {
                            $('#exponats-tags').prepend(newTag);
                        } else {
                            $('#exponats-tags').append(newTag);
                        }                      
                    }
                                      
                    $(this).addClass('active');
                    exhibit.setActiveFilter(fId, fItemId, 1);
                }

                return false;
            });           
        });
    },
    replaceExhibits: function () {
        $('.exponats-scroll .line > div').remove();
        exhibit.setExhibits(exhibit.objects);
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
    setFilters: function() {
        var elClone, elBlank = $('#exp-filter-blank');
        
        for (var id in exhibit.filters) {
            elClone = elBlank.clone();
            elClone.attr('id', 'fid-' + id);
            elClone.attr('data-fid', id);
            $('#exp-filter-blank').after(elClone);
            exhibit.setFilterResults(id);
        }
    },
    setFilterResults: function(id, letter) {
        var data, cl, el = $('#fid-' + id + ' .fiter-results').empty();
        
        for (var i in exhibit.filters[id].data) {
            cl = '';
            data = exhibit.filters[id].data[i];
            
            if (letter == undefined || letter.toLowerCase() == 'все' || data.title.substr(0, 1).toLowerCase() == letter.toLowerCase()) {
                if (exhibit.getActiveFilter(id, data.id) != undefined) {                   
                    cl = 'active';
                }
                
                el.append('<a class="' + cl + '" data-fid="' + id + '" data-fitemid="' + data.id + '" href="#">' + data.title + '</a>');
            }
        }
    },
    setActiveFilter: function(fId, fItemId, value) {
        if (exhibit.activeFilters[fId] == undefined) {
            exhibit.activeFilters[fId] = {};
        }
        
        exhibit.activeFilters[fId][fItemId] = value;
    },
    getActiveFilter: function(fId, fItemId) {
        if (exhibit.activeFilters[fId] == undefined) {
            return undefined;
        }
        
        return exhibit.activeFilters[fId][fItemId];
    },
    setExhibits: function(objects) {
        if (!objects.length) {
            exhibit.stopLoad = true;
            
            return false;
        }
        
        var elAppend, w, el, width = {},
                elClone = $('#el-one-blank').eq(0),
                type = $('#exp-types li.active a').attr('href').substr(1);
                      
        width['first'] = $('.exponats-scroll .first-line > div').size();
        width['second'] = $('.exponats-scroll .second-line > div').size();
        width['third'] = $('.exponats-scroll .third-line > div').size();              

        for (var i in objects) {
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
            el.find('a img').attr('src', objects[i]['img']);  
            el.find('.el-one-title').text(objects[i]['title']);
            el.find('.el-one-year').text(objects[i]['date']);
            el.find('.el-one-museum').text(objects[i]['museum']['title']);
            
            for (var a in objects[i]['authors']) {              
                el.find('.el-one-authors').append('<p><a href="">' + objects[i]['authors'][a]['title'] + '</a></p>');
            }
            
            elAppend.append(el);    
        }

        exhibit.addElOneHover();
    },
    apiReinitialise: function() {
        setTimeout(function () { exhibit.api.reinitialise(); }, 300);
    },
    clearExhibits: function() {
        $('.exponats-scroll .line > div').remove();
        exhibit.stopLoad = false;
        exhibit.offset = 0;
        exhibit.objects = [];
    },
    filterLoadExhibits: function() {   
        exhibit.clearExhibits();       
        exhibit.api.scrollToX(0, false);
        exhibit.apiReinitialise();
        exhibit.loadExhibits();
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
                filters: exhibit.activeFilters
            },
            dataType: 'json',
            success: function(data) { 
                exhibit.objects = $.merge(exhibit.objects, data.objects);
                exhibit.offset = data.offset;               
                exhibit.setExhibits(data.objects);
                
                if (!exhibit.stopLoad) {                   
                    exhibit.apiReinitialise();
                }           
            },
            complete: function() {
                exhibit.loading = false;
                armdMk.stopLoadingBlock();
            }
        });
    }
};