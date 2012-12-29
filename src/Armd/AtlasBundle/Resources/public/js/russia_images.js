var armdMkRussiaImages = {
    regionId: null,
    categoryIds: null,
    searchText: null,

    visibleCount: 0,
    loadByCount: 24,

    init: function () {
        armdMkRussiaImages.refreshVisibleCount();
        $('#show-more').bind('click', function(event) {
            event.preventDefault();
            armdMkRussiaImages.loadList(true);
        });

        $('#search-russia-images-button').bind('click', function(event) {
            event.preventDefault();

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

            armdMkRussiaImages.loadList(false);
        });
    },

//    startLoading: function () {
//        $('#images-of-russia-loading').show();
//    },
//
//    stopLoading: function () {
//        $('#images-of-russia-loading').hide();
//    },

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
        $.ajax({
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
                } else {
                    $('.obrazy-plitka').html(data);
                }
                if (!count) {
                    $('.more').hide();
                }
                armdMkRussiaImages.refreshVisibleCount();
            }
        });

        // full list append
        $.ajax({
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
                } else {
                    $('.full-list').html(data);
                }
            }
        });

        // short list append
        $.ajax({
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
                } else {
                    $('.short-list').html(data);
                }
            }
        });

    }
};