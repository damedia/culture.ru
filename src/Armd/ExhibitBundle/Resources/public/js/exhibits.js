var exhibit = {
    scrollPane: null,
    api: null,
    loading: false,
    offset: 0,
    stopLoad: false,
    objects: {},
    filters: {},
    activeFilters: {},
    tagsDelTimeout: null,
    imgLoadTimeout: null,
    firstLineWidth: 0,
    secondLineWidth: 0,
    thirdLineWidth: 0,
    oneLineImgHeight: 560,
    twoLineImgHeight: 318,
    threeLineImgHeight: 180,
    fullCount: 0,
    init: function(data, filters) {
        exhibit.objects = data.objects;
        exhibit.offset = data.offset;
        exhibit.filters = filters;
        
        $(function() {
            exhibit.setCount(data.count);
            exhibit.setExhibits(exhibit.objects);
            exhibit.scrollPane = $('.exponats-scroll-pane').jScrollPane({animateScroll: true});
            exhibit.api = exhibit.scrollPane.data('jsp');
            exhibit.apiReinitialise();
            
            exhibit.setFilters();         
        });

        $(window).load(function() {           
            //exhibit.scrollPane = $('.exponats-scroll-pane').jScrollPane({animateScroll: true});
            //exhibit.api = exhibit.scrollPane.data('jsp');
            //exhibit.apiReinitialise();
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
                    exhibit.api.scrollByX(delta * -200);
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
                    //exhibit.apiReinitialise();
                }

                return false;
            });

            $('#exp-categories').on('click', '.with-filter a', function() {
                var that = this, li = $(this).parent();

                if (li.hasClass('filtered')) {
                    li.removeClass('filtered');
                    $('#' + $(this).attr('data-id')).slideUp();
                    exhibit.searchClear();
                    exhibit.filterLoadExhibits();
                } else {
                    clearTimeout(exhibit.tagsDelTimeout);
                    li.addClass('filtered').siblings().removeClass('filtered');
                    
                    if ($('.exp-filter:visible').size()) {
                        $('.exp-filter:visible').slideUp(function() {
                            $('#' + $(that).attr('data-id')).show();
                        });
                    } else {
                        $('#' + $(this).attr('data-id')).show();
                    }
                }

                return false;
            });

            $('.exp-filter').on('click', '#filter-handle', function() {
                $('#exp-categories li').removeClass('filtered');
                $(this).parent().slideUp();
                exhibit.searchClear();
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
                exhibit.unsetActiveFilterTag($(this).parent());
                
                if ($('#exp-categories .filtered').size() == 0) {
                    exhibit.tagsDelTimeout = setTimeout(function() { exhibit.filterLoadExhibits(); }, 2000);
                }                            
            });

            $('.exp-filter .fiter-results').on('click', 'a', function() {
                if ($(this).hasClass('active')) {
                    exhibit.unsetActiveFilterTag($(this));
                } else {
                    exhibit.setActiveFilterTag($(this));
                }

                return false;
            });
            
            exhibit.searchClear();
            
            $('.obr-submit-input').click(function() {
                exhibit.searchLoadExhibits($.trim($('#search_text').val()));
            });
            
            $('#search_text').keypress(function(event) {
                if ( event.which == 13 ) {
                    exhibit.searchLoadExhibits($.trim($('#search_text').val()));
                    event.preventDefault();
                }               
            });
        });
    },
    setObjectListeners: function() {
        $('.el-one').hover(function() {
            $(this).addClass('el-one-hovered');
        }, function() {
            $(this).removeClass('el-one-hovered');
        });
        
        $('.el-one-text a').click(function() {
            if ($(this).attr('href') == '#') {
                exhibit.clearFilters();
                exhibit.setActiveFilterTag($(this));
                exhibit.searchClear();
                exhibit.filterLoadExhibits();     
            }
        });
    },
    setActiveFilterTag: function(tag) {
        var newTag, cl = '',
            fId = tag.attr('data-fid'),
            fItemId = tag.attr('data-fitemid');
    
        exhibit.activeFilters['search'] = undefined;
        
        if (exhibit.activeFilters[fId] == undefined) {
            exhibit.activeFilters[fId] = {};
        }
        
        exhibit.activeFilters[fId][fItemId] = 1;
        
        if (fId == 'museum') {
            cl = 'big-tag';
        }

        newTag = $('<li data-fid="' + fId + '" data-fitemid="' + fItemId + '" class="' + cl + '"><a href="#">' + tag.text() + '</a><span class="delete"></span></li>');

        if ($('#exponats-tags li[data-fid="' + fId + '"]').size()) {
            $('#exponats-tags li[data-fid="' + fId + '"]:last').after(newTag);
        } else {
            if (fId == 'museum') {
                $('#exponats-tags').prepend(newTag);
            } else {
                $('#exponats-tags').append(newTag);
            }                      
        }

        $('.fiter-results a').filter(function(index) {
            return ($(this).attr('data-fid') == fId && $(this).attr('data-fitemid') == fItemId);
        }).addClass('active');
    },
    unsetActiveFilterTag: function(tag) {
        var fId = tag.attr('data-fid'),
            fItemId = tag.attr('data-fitemid');
    
        if (exhibit.activeFilters[fId] != undefined) {
            exhibit.activeFilters[fId][fItemId] = undefined;
        }  
        
        $('#exponats-tags a').filter(function(index) {
            return ($(this).parent().attr('data-fid') == fId && $(this).parent().attr('data-fitemid') == fItemId);
        }).parent().remove();
        
        $('.fiter-results a').filter(function(index) {
            return ($(this).attr('data-fid') == fId && $(this).attr('data-fitemid') == fItemId);
        }).removeClass('active');
    },
    replaceExhibits: function () {
        $('.exponats-scroll .line > div').remove();
        exhibit.api.scrollToX(0, false);
        exhibit.firstLineWidth = exhibit.secondLineWidth = exhibit.thirdLineWidth = 0;
        exhibit.setExhibits(exhibit.objects);
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
                
                el.append('<a class="' + cl + '" data-fid="' + id + '" data-fitemid="' + data.id + '" href="#">' + data.title + '</a> ');
            }
        }
    },
    getActiveFilter: function(fId, fItemId) {
        if (exhibit.activeFilters[fId] == undefined) {
            return undefined;
        }
        
        return exhibit.activeFilters[fId][fItemId];
    },    
    setExhibits: function(objects) {
       // $(window).bind('load resize', function(){
        if($(window).height() > 500){
          var viewportH = $(window).height()-100;
          $('.exponats-scroll').height(viewportH);
          exhibit.oneLineImgHeight = viewportH - 60;
          exhibit.twoLineImgHeight = Math.round(viewportH/2) - 40;
          exhibit.threeLineImgHeight = Math.round(viewportH/3) - 40;
          alert($(window).height() + ', ' + exhibit.oneLineImgHeight + ', ' + exhibit.twoLineImgHeight  + ', ' + exhibit.threeLineImgHeight);
        }
        else{
          $('.exponats-scroll').height(400);
          exhibit.oneLineImgHeight = 340;
          exhibit.twoLineImgHeight = 160;
          exhibit.threeLineImgHeight = 93;
        }
       // });
        if ($.isEmptyObject(objects)) {
            exhibit.stopLoad = true;
            
            return false;
        }
        
        if (!exhibit.stopLoad && exhibit.getObjectsCount(exhibit.objects) >= exhibit.fullCount) {
            exhibit.stopLoad = true;           
        }
        
        var elAppend, w, h, el, c = 0,
            elClone = $('#el-one-blank').eq(0),
            type = $('#exp-types li.active a').attr('href').substr(1);                                         
                  
        for (var i in objects) {
            w = objects[i]['img_width'] / objects[i]['img_height'];
            
            if (type == 'threelines') {
                h = exhibit.threeLineImgHeight;
                
                if (exhibit.firstLineWidth <= exhibit.secondLineWidth && exhibit.firstLineWidth <= exhibit.thirdLineWidth) {                   
                    elAppend = '.first-line';
                    exhibit.firstLineWidth += w * h;
                } else if (exhibit.secondLineWidth <= exhibit.thirdLineWidth) {                    
                    elAppend = '.second-line';
                    exhibit.secondLineWidth += w * h;
                } else {                    
                    elAppend = '.third-line';
                    exhibit.thirdLineWidth += w * h;                 
                }                             
            } else if (type == 'twolines') {    
                h = exhibit.twoLineImgHeight;
                
                if (exhibit.firstLineWidth <= exhibit.secondLineWidth) {
                    elAppend = '.first-line';
                    exhibit.firstLineWidth += w * h;
                } else {
                    elAppend = '.second-line';
                    exhibit.secondLineWidth += w * h;
                }                               
            } else { 
                h = exhibit.oneLineImgHeight;
                elAppend = '.first-line';
                //exhibit.firstLineWidth += w * h;               
            }
            
            el = elClone.clone();           
            el.attr('style', '');
            el.find('a img').attr('src', objects[i]['img']);  
            el.find('a img').height(h);
            el.find('.el-one-title').html('<a href="' + Routing.generate('armd_exhibit_item', {'id': objects[i]['id']}) + '">' + objects[i]['title'] + '</a>');
            el.find('.el-one-img-link').attr('href', Routing.generate('armd_exhibit_item', {'id': objects[i]['id']}));
            el.find('.el-one-year').text(objects[i]['date']);
            el.find('.el-one-museum').attr('data-fid', 'museum');
            el.find('.el-one-museum').attr('data-fitemid', objects[i]['museum']['id']);
            el.find('.el-one-museum').text(objects[i]['museum']['title']);
            
            for (var a in objects[i]['authors']) {              
                el.find('.el-one-authors').append('<p><a href="#" data-fid="author" data-fitemid="' + objects[i]['authors'][a]['id'] + '">' + objects[i]['authors'][a]['title'] + '</a></p>');
            }
            
            if (exhibit.api) {
               exhibit.api.getContentPane().find(elAppend).append(el);
            } else {
                $('.exponats-scroll').find(elAppend).append(el);
            }                          

            el.find('> a img:first').load(function() {
                c++;
                clearTimeout(exhibit.imgLoadTimeout);

                if (c == exhibit.getObjectsCount(objects) && exhibit.stopLoad) {
                    if (exhibit.api) {
                        exhibit.api.getContentPane().find(elAppend).append('<div class="el-one" style="width: 260px"></div>');
                    } else {
                        $('.exponats-scroll').find(elAppend).append('<div class="el-one" style="width: 260px"></div>');
                    }                   
                }
                
                exhibit.imgLoadTimeout = exhibit.apiReinitialise();                              
            });
            
        }

        exhibit.setObjectListeners();  
    },
    getObjectsCount: function(objects) {
        var count = 0;
        
        $.each(objects, function(key, value) {
            count++;
        });
        
        return count;
    },
    apiReinitialise: function(timeout) {
        if (exhibit.api == undefined || $('.exponats-scroll-pane').data('jsp') == undefined) {
            return 0;
        }
        
        if (timeout == undefined) {
            timeout = 100;
        }
        
        return setTimeout(function () { 
            exhibit.api = $('.exponats-scroll-pane').data('jsp'); 
            exhibit.api.reinitialise();  
        }, timeout);
    },
    clearExhibits: function() {
        $('.exponats-scroll .line > div').remove();
        exhibit.stopLoad = false;
        exhibit.offset = 0;
        exhibit.objects = {};
        exhibit.firstLineWidth = exhibit.secondLineWidth = exhibit.thirdLineWidth = 0;
    },
    clearFilters: function() {
        exhibit.activeFilters = {};
        $('#exponats-tags').empty();
        $('.fiter-results a').removeClass('active');
        $('.exp-filter:visible').slideUp();
        $('#exp-categories .filtered').removeClass('filtered');
    },
    searchLoadExhibits: function(text) {
        exhibit.clearFilters();
        
        if (text) {
            exhibit.activeFilters = {'search': text};
        }
        
        exhibit.filterLoadExhibits();
    },
    searchClear: function() {
        exhibit.activeFilters['search'] = undefined;
        $('#search_text').val('');
    },
    filterLoadExhibits: function() {   
        exhibit.clearExhibits();       
        exhibit.api.scrollToX(0, false);
        exhibit.api.reinitialise();
        exhibit.loadExhibits();
    },
    setCount: function(count) {
        exhibit.fullCount = count;
        $('.exponats-total').text('Всего ' + count);
    },
    loadExhibits: function() {
        if (exhibit.loading || exhibit.stopLoad) {
            return false;
        }

        exhibit.loading = true;
        armdMk.startLoadingBlock($('#exponats-content'));
        var jqxhr = $.ajax({
            url: Routing.generate('armd_load_exhibits', {'offset': exhibit.offset}),
            type: 'post',
            data: {
                filters: exhibit.activeFilters
            },
            dataType: 'json'
        })
        .done(function(data) { 
            //exhibit.objects = $.merge(exhibit.objects, data.objects);
            $.extend(exhibit.objects, data.objects);
            
            if (exhibit.offset === 0) {
                exhibit.setCount(data.count);
            }

            exhibit.offset = data.offset;               
            exhibit.setExhibits(data.objects);

            //exhibit.apiReinitialise();           
        })
        .always(function() {
            exhibit.loading = false;
            armdMk.stopLoadingBlock($('#exponats-content'));           
        });
        
        return jqxhr;
    }
};