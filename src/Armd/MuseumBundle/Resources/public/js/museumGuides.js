var armdMuseumGuides = {

    init: function () {
        armdMuseumGuides.initUi();
    },

    initUi: function () {
        $('#museums-filter-form').ajaxForm({
            beforeSubmit: function(){
                armdMk.startLoading();
                armdMuseumGuides.resetTextFilter();
                armdMuseumGuides.startLoading();
            },
            success: function(data) {
                $('#museums-container').html(data);
                armdMuseumGuides.stopLoading();
                //armdMuseumGuides.initLoadedUi();
                armdMk.stopLoading();
            }
        });
        // filter
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-filter-city"] a, .ui-selectgroup-list[aria-labelledby="ui-filter-museum"] a',
            function (event) {
                armdMuseumGuides.resetTextFilter();
                armdMuseumGuides.isSearch = false;
                if ($(event.target).closest('.ui-selectgroup-list').attr('aria-labelledby') === 'ui-filter-city') {
                    armdMuseumGuides.loadMuseumList();
                }
                armdMuseumGuides.loadList(false, false);

            });

        // init search
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMuseumGuides.loadTextFilterResult();
            }
        });
    },

    resetFilter: function() {
        $('#filter-city').val('').selectgroup('refresh');
        $('#filter-museum').val('').selectgroup('refresh');
    },

    resetTextFilter: function() {
        $('#search-txt').val('').selectgroup('refresh');
    },

    startLoading: function () {
        $('#museums-loading').show();
    },

    stopLoading: function () {
        $('#museums-loading').hide();
    },

    loadTextFilterResult: function() {
        var searchQuery = $('#search-txt').val().trim();
        if (searchQuery.length > 0) {
            armdMuseumGuides.resetFilter();
            armdMuseumGuides.startLoading();
            $.ajax({
                url: Routing.generate('armd_museum_guide_list'),
                data: {'search_query': searchQuery},
                dataType: 'html',
                method: 'get',
                success: function(data) {$('#museums-container').html(data);},
                complete: function() {armdMuseumGuides.stopLoading();}
            });
        }
    },
    
    loadList: function(isSearch) {
        armdMuseumGuides.startLoading();
        var data = {};

        if (isSearch) {
            data['search_query'] = $('#search-txt').val();
        } else {
            data['cityId'] = $('#filter-city').val();
            data['museumId'] = $('#filter-museum').val();
        }

        $.ajax({
            url: Routing.generate('armd_museum_guide_list'),
            data: data,
            type: 'get',
            dataType: 'html',
            success: function(data) {
                $('#museums-container').html(data);

                if ($.trim(data).length === 0) {
                    $('#museums-container').html('<h2>Не найдено</h2>');
                }
            },
            complete: function() {
                armdMk.stopLoading();
            }
        });
    },
    
    loadMuseumList: function () {
        var select = $('#filter-museum');
        if (select.length) {
            var cityId = $('#filter-city').val();
            select.html('<option value="0">Все</option>');
            if (cityId > 0) {
                armdMuseumGuides.startLoading();
                $.ajax({
                    url: Routing.generate('armd_museum_guide_museums', {'cityId': cityId}),
                    dataType: 'json',
                    success: function (data) {
                        for (var i in data) {
                            select.append($('<option>', {value: data[i].id}).text(data[i].title));
                        }
                        select.selectgroup('refresh');
                    },
                    complete: function () {
                        armdMuseumGuides.stopLoading();
                    }
                });
            }
        }
    }

};
