var armdMkRussiaImages = {
    regionId: null,
    categoryIds: null,
    searchText: null,

    visibleCount: 0,
    loadByCount: 24,


    init: function () {
        armdMkRussiaImages.refreshVisibleCount();

        // init "more" click
        $('#show-more').bind('click', function(event) {
            event.preventDefault();
            armdMkRussiaImages.loadList(true);
        });

        // russia images search button
        $('#search-russia-images-button').bind('click', function(event) {
            event.preventDefault();

            armdMkRussiaImages.readFilter();
            armdMkRussiaImages.loadList(false);
        });

        // object region click
        $('.object-region').bind('click', function (event) {
            event.preventDefault();
            var regionId = $(this).data('region-id');

            $('#object-region').val(regionId).selectgroup('refresh');
            $('#object-type').val('').selectgroup('refresh');
            $('#object-thematic').val('').selectgroup('refresh');

            armdMkRussiaImages.readFilter();
            armdMkRussiaImages.loadList(false);
        });
    },

    readFilter: function () {
        var regionId = $('#object-region').val();
        armdMkRussiaImages.regionId = regionId ? regionId : null;

        var categoryId;
        var categoryIds = [];

        categoryId = $('#object-thematic').val();
        if (categoryId) {
            categoryIds.push(categoryId);
        }

        categoryId = $('#object-type').val();
        if (categoryId) {
            categoryIds.push(categoryId);
        }

        armdMkRussiaImages.categoryIds = categoryIds;
    },

    startLoading: function () {
        armdMk.startLoadingBlock('body');
        $('#search-russia-images-button').addClass('loading');
    },

    stopLoading: function () {
        armdMk.stopLoadingBlock('body');
        $('#search-russia-images-button').removeClass('loading');
    },

    refreshVisibleCount: function() {
        armdMkRussiaImages.visibleCount = $('.plitka-one-wrap').length;
        $('#found-objects-count').text(armdMkRussiaImages.visibleCount);
    },

    loadList: function (append) {
        var data = {
            region_id: armdMkRussiaImages.regionId,
            category_ids: armdMkRussiaImages.categoryIds,
            search_query: armdMkRussiaImages.searchText
        };

        var offset = append ? armdMkRussiaImages.visibleCount : 0;

        // tile append
        var tileParams = {
            url: Routing.generate('armd_atlas_russia_images_list', {
                'templateName': 'tile',
                'offset': offset,
                'limit': armdMkRussiaImages.loadByCount
            }),
            cache: false,
            dataType: 'html',
            type: 'get',
            data: data,
            success: function (data) {
                var count = $(data).filter('.plitka-one-wrap').length;

                if (append) {
                    if (count) {
                        $('.obrazy-plitka').append(data);
                    }
                }
                else {
                    $('.obrazy-plitka').html(data);
                }
                if (!count) {
                    $('.more').hide();
                }
                armdMkRussiaImages.refreshVisibleCount();
            }
        };

        // full list append
        var fullList = {
            url: Routing.generate('armd_atlas_russia_images_list', {
                'templateName': 'full-list',
                'offset': offset,
                'limit': armdMkRussiaImages.loadByCount
            }),
            cache: false,
            dataType: 'html',
            type: 'get',
            data: data,
            success: function (data) {
                var count = $(data).filter('.full-list-one').length
                if (append) {
                    if (count) {
                        $('.full-list').append(data);
                    }
                }
                else {
                    $('.full-list').html(data);
                }
            }
        };

        // short list append
        var shortListParams = {
            url: Routing.generate('armd_atlas_russia_images_list', {
                'templateName': 'short-list',
                'offset': offset,
                'limit': armdMkRussiaImages.loadByCount
            }),
            cache: false,
            dataType: 'html',
            type: 'get',
            data: data,
            success: function (data) {
                var count = $(data).filter('.full-list-one').length;
                if (append) {
                    if (count) {
                        $('.short-list').append(data);
                    }
                }
                else {
                    $('.short-list').html(data);
                }
            }
        };


        armdMkRussiaImages.startLoading();
        $.when($.ajax(tileParams), $.ajax(fullList), $.ajax(shortListParams))
            .done(function () {
                armdMkRussiaImages.stopLoading();
            })
            .fail(function () {
                armdMkRussiaImages.stopLoading();
            });
    }
};