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
            armdMkRussiaImages.resetTextFilter();
            armdMkRussiaImages.loadList(false);
        });

        // object region click
        $('.object-region').bind('click', function (event) {
            event.preventDefault();
            var regionId = $(this).data('region-id');

            armdMkRussiaImages.resetFilter();
            armdMkRussiaImages.resetTextFilter();
            $('#object-region').val(regionId).selectgroup('refresh');
            armdMkRussiaImages.readFilter();

            armdMkRussiaImages.loadList(false);
        });

        // init search
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMkRussiaImages.resetFilter();
                armdMkRussiaImages.readTextFilter();
                armdMkRussiaImages.loadList(false);
            }
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

    resetFilter: function () {
        $('#object-region').val('').selectgroup('refresh');
        $('#object-type').val('').selectgroup('refresh');
        $('#object-thematic').val('').selectgroup('refresh');

        armdMkRussiaImages.readFilter();
    },

    readTextFilter: function () {
        armdMkRussiaImages.searchText = $('#search-txt').val();
    },

    resetTextFilter: function() {
        armdMkRussiaImages.searchText = '';
        $('#search-txt').val('');
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
        armdMkRussiaImages.visibleCount = $('.rusObr-list li').length;
        $('#found-objects-count').text(armdMkRussiaImages.visibleCount);
    },

    loadList: function (append) {
        var data = {
            region_id: armdMkRussiaImages.regionId,
            category_ids: armdMkRussiaImages.categoryIds,
            search_text: armdMkRussiaImages.searchText
        };

        var offset = append ? armdMkRussiaImages.visibleCount : 0;

        

        // full list append
        var specialList = {
            url: Routing.generate('armd_atlas_russia_images_list', {
                'templateName': 'special-list',
                'offset': offset,
                'limit': armdMkRussiaImages.loadByCount
            }),
            cache: false,
            dataType: 'html',
            type: 'get',
            data: data,
            success: function (data) {
                var count = $(data).filter('li').length;
                console.log(count);
                if (append) {
                    if (count) {
                        $('.rusObr-list').append(data);
                    }
                }
                else {
                    $('.rusObr-list').html(data);
                }
                if (!count || count < armdMkRussiaImages.loadByCount) {
                    $('.more').hide();
                }
                armdMkRussiaImages.refreshVisibleCount();
            }
        };

        

        armdMkRussiaImages.startLoading();
       $.when($.ajax(specialList))
          //$.when($.ajax(tileParams), $.ajax(fullList), $.ajax(shortListParams))
            .done(function () {
                armdMkRussiaImages.stopLoading();
            })
            .fail(function () {
                armdMkRussiaImages.stopLoading();
            });
    }
};