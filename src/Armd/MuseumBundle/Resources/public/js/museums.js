var armdMuseums = {

    regionId: null,
    categoryId: null,
    searchText: null,

    visibleCount: 0,
    loadByCount: 10,

    init: function () {
        armdMuseums.refreshVisibleCount();
        armdMuseums.totalCount();
        
        // init "more" click
        $('#show-more').bind('click', function(event) {
            event.preventDefault();
            armdMuseums.loadList(true, false);
        });
        
        $('#museums-filter-form').ajaxForm({
            beforeSubmit: function(){
                armdMk.startLoading();
                armdMuseums.resetTextFilter();
                armdMuseums.startLoading();
                armdMuseums.readFilter();
            },
            success: function(data) {
                armdMuseums.loadList(false, false);
                armdMuseums.stopLoading();
                armdMk.stopLoading();
            }
        });
        
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMuseums.resetFilter();
                armdMuseums.readTextFilter();
                armdMuseums.loadList(false, false);
            }
        });
        
        $('#show-all').on('click', $(this), function (event) {
            armdMuseums.loadList(false, true);
            return false;
        });
    
        armdMuseums.initUi();
    },

    
    readFilter: function () {
        var regionId = $('#filter-region').val();
        armdMuseums.regionId = regionId ? regionId : null;

        var categoryId = $('#filter-category').val();
        armdMuseums.categoryId = categoryId ? categoryId : null;

        
    },

    resetFilter: function () {
        $('#filter-region').val('').selectgroup('refresh');
        $('#filter-category').val('').selectgroup('refresh');

        armdMuseums.readFilter();
    },

    readTextFilter: function () {
        armdMuseums.searchText = $('#search-txt').val();
    },

    resetTextFilter: function() {
        armdMuseums.searchText = '';
        $('#search-txt').val('').selectgroup('refresh');
    },

    startLoading: function () {
        armdMk.startLoadingBlock('body');
        $('#museums-loading').show();
    },

    stopLoading: function () {
        armdMk.stopLoadingBlock('body');
        $('#museums-loading').hide();
    },
    

    refreshVisibleCount: function() {
        armdMuseums.visibleCount = $('.plitka-one-wrap').length;
        $('#found-objects-count').text(armdMuseums.visibleCount);
    },
    
    totalCount: function(found){
        
        $('#found-objects-total').addClass('loading');
        $('#found-objects-total span').html('');
        
        if (found || found === 0) {
            $('#found-objects-total').removeClass('loading'); 
            $('#found-objects-total span').html(found); 
        } else {
            $.ajax({
                url: Routing.generate('armd_museum_list', {'limit':10000, 'templateName': 'virtual_list'}),
                cache: false,
                dataType: 'html',
                type: 'get',
                data: {
                    region: armdMuseums.regionId,
                    category: armdMuseums.categoryId,
                    search_query: armdMuseums.searchText
                }
            }).done(function (data) {
                $('#found-objects-total').removeClass('loading'); 
                $('#found-objects-total span').html($(data).filter('.virt-museum-new-list-one').length);
            });
        }    
    },
    
    loadList: function (append, fullList) {
        var data = {
            region: armdMuseums.regionId,
            category: armdMuseums.categoryId,
            search_query: armdMuseums.searchText
        };

        var offset = append ? armdMuseums.visibleCount : 0;
        var limit = fullList ?  10000 : (armdMuseums.loadByCount + 1);   
        
        // tile append
        var tileParams = {
            url: Routing.generate('armd_museum_list', {
                'templateName': 'virtual_list',
                'offset': offset,
                'limit': limit
            }),
            cache: false,
            dataType: 'html',
            type: 'get',
            data: data,
            success: function (data) {
                var length = $(data).filter('.virt-museum-new-list-one').length,
                    count,
                    newData = $(data);
                    
                if (length > limit - 1) {
                    count = length - 1;
                    newData = $(data).filter('.virt-museum-new-list-one:lt('+count+')');
                    //armdMuseums.totalCount(false);
                } else {
                    //armdMuseums.totalCount(length);
                    count = length;
                }    
                
                if (append) {
                    if (count) {
                        $('.virt-museum-new-list').append(newData);
                    }
                }
                else {
                    $('.virt-museum-new-list').html(newData);
                }
                if (!count || length <= armdMuseums.loadByCount || fullList) {

                    $('.more').hide();
                    $('#show-all').hide();
                } else {
                    $('.more').show();
                    $('#show-all').show();
                }
                armdMuseums.refreshVisibleCount();
                
            }
        };
        // full list append
        var listParam = {
            url: Routing.generate('armd_museum_list', {
                'templateName': 'virtual_list_text',
                'offset': offset,
                'limit': limit
            }),
            cache: false,
            dataType: 'html',
            type: 'get',
            data: data,
            success: function (data) {
                var count = $(data).filter('.museum-list-wrapper').length;
                if (append) {
                    if (count) {
                        $('.virt-museum-new-list-text').append(data);
                    }
                }
                else {
                    $('.virt-museum-new-list-text').html(data);
                }
            }
        };

        armdMuseums.startLoading();
        $.when($.ajax(tileParams) && $.ajax(listParam))
            .done(function () {
                armdMuseums.stopLoading();
            })
            .fail(function () {
                armdMuseums.stopLoading();
            });
    },
    
    initUi: function () {
        if ($('.vob').length) {
            $('.vob').fancybox({
                'autoScale': false,
                'autoDimensions': false,
                'padding': 0,
                'margin': 0,
                'fitToView': true
            });
        }

        $(".iframe").fancybox({
            'width': '100%',
            'height': '100%',
            'autoScale': true,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'
        });
    },
   
    
    refreshVisibleCount: function() {
        armdMuseums.visibleCount = $('.virt-museum-new-list-one').length;
        $('#found-objects-count').text(armdMuseums.visibleCount);
    },

    
    loadTextFilterResult: function() {
        var searchQuery = $('#search-txt').val().trim();
        if (searchQuery.length > 0) {
            armdMuseums.resetFilter();
            armdMk.startLoading();
            $.ajax({
                url: Routing.generate('armd_museum_list'),
                data: {
                    'search_query': searchQuery
                },
                dataType: 'html',
                method: 'get',
                success: function(data) {
                    $('#museums-container').html(data);
                    $('#category-chooser a.active').trigger('click');
                },
                complete: function() {
                    armdMk.stopLoading();
                }
            })
        }
    }

};
