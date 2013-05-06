var armdMkRussiaImages = {
    regionId: null,
    categoryIds: null,
    searchText: null,

    visibleCount: 0,
    loadByCount: 30,


    init: function () {
        armdMkRussiaImages.refreshVisibleCount();
        //armdMkRussiaImages.totalCount();

        // init "more" click
        $('#show-more').bind('click', function(event) {
            event.preventDefault();
            armdMkRussiaImages.loadList(true, false);
        });

        // russia images search button
        $('#search-russia-images-button').bind('click', function(event) {
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
        $('#search-form').bind('submit', function(event) {
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
        armdMkRussiaImages.visibleCount = $('.plitka-one-wrap').length;
        $('#found-objects-count').text(armdMkRussiaImages.visibleCount);
    },
    
    totalCount: function(found){
        
        $('#found-objects-total').addClass('loading');
        $('#found-objects-total span').html('');
        
        if (found || found === 0) {
            $('#found-objects-total').removeClass('loading'); 
            $('#found-objects-total span').html(found); 
        } else {
            var data = {
                region_id: armdMkRussiaImages.regionId,
                category_ids: armdMkRussiaImages.categoryIds,
                search_text: armdMkRussiaImages.searchText
            };

            var countTotal = {
                url: Routing.generate('armd_atlas_russia_images_list', {
                    'templateName': 'short-list',
                    'offset': 0,
                    'limit': 10000
                }),
                cache: false,
                dataType: 'html',
                type: 'get',
                data: data,
                success: function (data) {
                    var length = $(data).filter('.full-list-one').length;
                    $('#found-objects-total').removeClass('loading'); 
                    $('#found-objects-total span').html(length);
                                   
                }
            };  
            $.ajax(countTotal);
        }    
    },
    
    

    loadList: function (append, fullList) {
        var data = {
            region_id: armdMkRussiaImages.regionId,
            category_ids: armdMkRussiaImages.categoryIds,
            search_text: armdMkRussiaImages.searchText
        };

        var offset = append ? armdMkRussiaImages.visibleCount : 0;
        var limit = fullList ?  10000 : (armdMkRussiaImages.loadByCount + 1);   
        
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
                var length = $(data).filter('.plitka-one-wrap').length,
                    count,
                    newData = $(data);
                    
                if (length > limit - 1) {
                    count = length - 1;
                    newData = $(data).filter('.plitka-one-wrap:lt('+count+')');
                    armdMkRussiaImages.totalCount(false);
                } else {
                    armdMkRussiaImages.totalCount(length);
                }    
                
                if (append) {
                    if (count) {
                        $('.obrazy-plitka').append(newData);
                    }
                }
                else {
                    $('.obrazy-plitka').html(newData);
                }

                if (!count || length <= armdMkRussiaImages.loadByCount) {
                    $('.more').hide();
                    $('#show-all').hide();
                } else {
                    $('.more').show();
                    $('#show-all').show();
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




if ($('.plitka-one-wrap').length < armdMkRussiaImages.loadByCount) {
   // $('.more').hide();
}
