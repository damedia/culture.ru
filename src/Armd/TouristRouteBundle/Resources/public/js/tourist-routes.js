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

        $("#route-region").chosen({
            no_results_text: "Не найдено"
        });

        // filters
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-route-category"] a', function(event) {
                armdMkTouristRoutes.readFilter();
                armdMkTouristRoutes.resetTextFilter();
                armdMkTouristRoutes.loadList(false, false);
        });

        $("#route-region").change(function(event) {
            armdMkTouristRoutes.readFilter();
            armdMkTouristRoutes.resetTextFilter();
            armdMkTouristRoutes.loadList(false, false);
        })

        // category click
        $('.main').on('click', '.route-category', function (event) {
            event.preventDefault();
            var categoryId = $(this).data('category-id');

            armdMkTouristRoutes.resetFilter();
            armdMkTouristRoutes.resetTextFilter();
            $('#route-category').val(categoryId).selectgroup('refresh');
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
    },

    stopLoading: function () {
        armdMk.stopLoadingBlock('body');
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
