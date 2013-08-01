var armdMkRussiaImages = {
    regionId: null,
    categoryIds: null,
    searchText: null,

    visibleCount: 0,
    totalCount: 0,
    loadByCount: 30,


    init: function () {
        armdMkRussiaImages.refreshVisibleCount();
        //armdMkRussiaImages.totalCount();

        // init "more" click
        $('#show-more').bind('click', function (event) {
            event.preventDefault();
            armdMkRussiaImages.loadList(true, false);
        });

        // russia images search button
        $('#search-russia-images-button').bind('click', function (event) {
            event.preventDefault();

            armdMkRussiaImages.readFilter();
            armdMkRussiaImages.resetTextFilter();
            armdMkRussiaImages.loadList(false, false);
        });

        // object region click
        $('.main').on('click', '.object-region', function (event) {
            event.preventDefault();
            var regionId = $(this).data('region-id');

            armdMkRussiaImages.resetFilter();
            armdMkRussiaImages.resetTextFilter();
            $('#object-region').val(regionId).selectgroup('refresh');
            armdMkRussiaImages.readFilter();

            armdMkRussiaImages.loadList(false, false);
        });

        // init search
        $('#search-form').bind('submit', function (event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMkRussiaImages.resetFilter();
                armdMkRussiaImages.readTextFilter();
                armdMkRussiaImages.loadList(false, false);
            }
        });

        $('#show-all').on('click', $(this), function (event) {
            armdMkRussiaImages.loadList(false, true);
            return false;
        });

        // init filters for "more" and "all records" buttons
        if ($('#search-txt').val()) {
            this.resetFilter();
            this.readTextFilter();
            $('#search-this-section').prop('checked', 'checked');
        } else {
            this.resetTextFilter();
            this.readFilter();
        }

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

    resetTextFilter: function () {
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

    refreshVisibleCount: function () {
        this.visibleCount = $('.plitka-one-wrap').length;

        var lastDataElement = $('.plitka-one-wrap[data-count]:last');
        if (lastDataElement) {
            this.totalCount = lastDataElement.data('total-count');
        }

        $('#found-objects-count').text(this.visibleCount);
        $('#found-objects-total span').text(this.totalCount);

        if (this.visibleCount >= this.totalCount) {
            $('.more').hide();
            $('#show-all').hide();
        } else {
            $('.more').show();
            $('#show-all').show();
        }
    },

    loadList: function (append, fullList) {
        var data = {
            region_id: armdMkRussiaImages.regionId,
            category_ids: armdMkRussiaImages.categoryIds,
            search_text: armdMkRussiaImages.searchText
        };

        var offset = append ? armdMkRussiaImages.visibleCount : 0;
        var limit = fullList ? 10000 : armdMkRussiaImages.loadByCount;

        // tile append
        var tileParams = {
            url: Routing.generate('armd_atlas_russia_images_list', {
                'templateName': 'tile',
                'offset': offset,
                'limit': limit
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

                armdMkRussiaImages.refreshVisibleCount();
            }
        };

        // full list append
        var fullList = {
            url: Routing.generate('armd_atlas_russia_images_list', {
                'templateName': 'full-list',
                'offset': offset,
                'limit': limit
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
                'limit': limit
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

