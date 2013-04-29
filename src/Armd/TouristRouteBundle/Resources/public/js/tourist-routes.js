var armdMkTouristRoutes = {
    regionId:     null,
    categoryId:   null,
    searchText:   null,
    visibleCount: 0,
    loadByCount:  30,


    init: function () {
        armdMkTouristRoutes.refreshVisibleCount();
        armdMkTouristRoutes.totalCount();

        var count = $('.plitka-one-wrap').length;

        if (!count || count <= armdMkTouristRoutes.loadByCount) {
            $('.more').hide();
            $('#show-all').hide();
        }

        // init "more" click
        $('#show-more').bind('click', function(event) {
            event.preventDefault();
            armdMkTouristRoutes.loadList(true, false);
        });

        // search button
        $('#search-routes-button').bind('click', function(event) {
            event.preventDefault();

            armdMkTouristRoutes.readFilter();
            armdMkTouristRoutes.resetTextFilter();
            armdMkTouristRoutes.loadList(false, false);
        });

        // region click
        $('.main').on('click', '.route-region', function (event) {
            event.preventDefault();
            var regionId = $(this).data('region-id');

            armdMkTouristRoutes.resetFilter();
            armdMkTouristRoutes.resetTextFilter();
            $('#route-region').val(regionId).selectgroup('refresh');
            armdMkTouristRoutes.readFilter();

            armdMkTouristRoutes.loadList(false, false);
        });

        // init search
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMkTouristRoutes.resetFilter();
                armdMkTouristRoutes.readTextFilter();
                armdMkTouristRoutes.loadList(false, false);
            }
        });
        
        $('#show-all').on('click', $(this), function (event) {
            armdMkTouristRoutes.loadList(false, true);

            return false;
        });

    },

    readFilter: function () {
        var regionId   = $('#route-region').val(),
            categoryId = $('#route-category').val();

        armdMkTouristRoutes.regionId   = regionId ? regionId : null;
        armdMkTouristRoutes.categoryId = categoryId ? categoryId : null;
    },

    resetFilter: function () {
        $('#route-region, #route-category').val('').selectgroup('refresh');
        armdMkTouristRoutes.readFilter();
    },

    readTextFilter: function () {
        armdMkTouristRoutes.searchText = $('#search-txt').val();
    },

    resetTextFilter: function() {
        armdMkTouristRoutes.searchText = '';
        $('#search-txt').val('');
    },

    startLoading: function () {
        armdMk.startLoadingBlock('body');
        $('#search-routes-button').addClass('loading');
    },

    stopLoading: function () {
        armdMk.stopLoadingBlock('body');
        $('#search-routes-button').removeClass('loading');
    },

    refreshVisibleCount: function() {
        armdMkTouristRoutes.visibleCount = $('.plitka-one-wrap').length;
        $('#found-routes-count').text(armdMkTouristRoutes.visibleCount);
    },
    
    totalCount: function(found) {
        $('#found-routes-total').addClass('loading');
        $('#found-routes-total span').html('');
        
        if (found || found === 0) {
            $('#found-routes-total').removeClass('loading');
            $('#found-routes-total span').html(found);

        } else {
            $.ajax({
                url: Routing.generate('armd_tourist_route_ajax_list', {
                    'offset': 0,
                    'limit': 10000
                }),
                data: {
                    region_id:    armdMkTouristRoutes.regionId,
                    category_id:  armdMkTouristRoutes.categoryId,
                    search_text:  armdMkTouristRoutes.searchText
                },
                cache:    false,
                dataType: 'html',
                type:     'get'
            })
                .done(function(data) {
                    var length = $(data).filter('.plitka-one-wrap').length;
                    $('#found-routes-total').removeClass('loading');
                    $('#found-routes-total span').html(length);
                });
        }    
    },

    loadList: function (append, fullList) {
        var offset = append ? armdMkTouristRoutes.visibleCount : 0;
        var limit = fullList ?  10000 : (armdMkTouristRoutes.loadByCount + 1);   
        
        armdMkTouristRoutes.startLoading();

        $.ajax({
            url: Routing.generate('armd_tourist_route_ajax_list', {
                'offset': offset,
                'limit': limit
            }),
            data: {
                region_id:    armdMkTouristRoutes.regionId,
                category_id:  armdMkTouristRoutes.categoryId,
                search_text:  armdMkTouristRoutes.searchText
            },
            cache:    false,
            dataType: 'html',
            type:     'get',
        })
            .always(function () {
                armdMkTouristRoutes.stopLoading();
            })
            .done(function (data) {
                var length = $(data).filter('.plitka-one-wrap').length,
                    count,
                    newData = $(data);
                    
                if (length > limit - 1) {
                    count = length - 1;
                    newData = $(data).filter('.plitka-one-wrap:lt(' + count + ')');
                    armdMkTouristRoutes.totalCount(false);
                } else {
                    armdMkTouristRoutes.totalCount(length);
                }    
                
                if (append) {
                    if (count) {
                        $('.obrazy-plitka').append(newData);
                    }

                } else {
                    $('.obrazy-plitka').html(newData);
                }

                if (!count || length <= armdMkTouristRoutes.loadByCount) {
                    $('.more').hide();
                    $('#show-all').hide();

                } else {
                    $('.more').show();
                    $('#show-all').show();
                }

                armdMkTouristRoutes.refreshVisibleCount();
            });
    }
};
