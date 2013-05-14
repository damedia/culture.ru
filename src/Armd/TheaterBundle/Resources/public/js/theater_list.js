var armdMkTheaterList = {
    city: null,
    category: null,
    searchText: null,
    order: 'date',

    visibleCount: 0,
    loadByCount: 0,


    init: function (limit) {
        armdMkTheaterList.loadByCount = limit;
        armdMkTheaterList.refreshVisibleCount();
        
        if (armdMkTheaterList.visibleCount >= armdMkTheaterList.loadByCount) {
            $('.more').show();
        }

        // init "more" click
        $('#show-more').bind('click', function(event) {
            event.preventDefault();
            
            armdMkTheaterList.readFilter();
            armdMkTheaterList.loadList(true);
        });
        
        // filter
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-filter-city"] a, .ui-selectgroup-list[aria-labelledby="ui-filter-category"] a',
            function (event) {
                event.preventDefault();
    
                armdMkTheaterList.readFilter();
                armdMkTheaterList.resetTextFilter();
                armdMkTheaterList.loadList(false);

        });        

        // object region click
        $('.main').on('click', '.object-region', function (event) {
            event.preventDefault();
            
            var cityId = $(this).data('city-id');

            armdMkTheaterList.resetFilter();
            armdMkTheaterList.resetTextFilter();
            $('#filter-city').val(cityId).selectgroup('refresh');
            armdMkTheaterList.readFilter();

            armdMkTheaterList.loadList(false);
        });
        
        $('.main').on('click', '.plitka-one-wrap .tags a', function (event) {
            event.preventDefault();
            
            var categoryId = $(this).data('category-id');

            armdMkTheaterList.resetFilter();
            armdMkTheaterList.resetTextFilter();
            $('#filter-category').val(categoryId).selectgroup('refresh');
            armdMkTheaterList.readFilter();

            armdMkTheaterList.loadList(false);
        });

        // init search
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMkTheaterList.resetFilter();
                armdMkTheaterList.readTextFilter();
                armdMkTheaterList.loadList(false);
            }
        });
        
        $('.sort-filter a').bind('click', function(event) {
            event.preventDefault();
            armdMkTheaterList.order = $(this).data('order');
            $(this).addClass('active').siblings().removeClass('active');
            armdMkTheaterList.loadList(false);           
        });
    },

    readFilter: function () {
        var city = $('#filter-city').val();
        armdMkTheaterList.city = city ? city : null;

        var category = $('#filter-category').val();
        armdMkTheaterList.category = category ? category : null;
    },

    resetFilter: function () {
        $('#filter-city').val('').selectgroup('refresh');
        $('#filter-category').val('').selectgroup('refresh');

        armdMkTheaterList.readFilter();
    },

    readTextFilter: function () {
        armdMkTheaterList.searchText = $('#search-txt').val();
    },

    resetTextFilter: function() {
        armdMkTheaterList.searchText = '';
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
        armdMkTheaterList.visibleCount = $('.plitka-one-wrap').length;
    },

    loadList: function (append) {        
        var offset = append ? armdMkTheaterList.visibleCount : 0;
        
        armdMkTheaterList.startLoading();

        var jqxhr = $.ajax({
            url: Routing.generate('armd_theater_list_data', {
                'offset': offset,
                'limit': armdMkTheaterList.loadByCount
            }),
            type: 'get',
            cache: false,            
            dataType: 'html',
            data: {
                order: armdMkTheaterList.order,
                city: armdMkTheaterList.city,
                category: armdMkTheaterList.category,
                search_text: armdMkTheaterList.searchText
            }
        })
        .done(function(data) { 
            var count = $(data).filter('.plitka-one-wrap').length;
            
            if (append) {
                if (count) {
                    $('.obrazy-plitka').append(data);
                }
            }
            else {
                $('.obrazy-plitka').html(data);
            }
            
            if (!count || count < armdMkTheaterList.loadByCount) {
                $('.more').hide();
            } else {
                $('.more').show();
            }
            
            armdMkTheaterList.refreshVisibleCount();           
        })
        .always(function() {
            armdMkTheaterList.stopLoading();           
        });
        
        return jqxhr;
    }
};
