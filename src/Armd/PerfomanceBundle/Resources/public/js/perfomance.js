var armdMkPerfomance = {
    isSearch: false,

    init: function(count) {

    	armdMkPerfomance.loadByCount = count;
    	
        // filter
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-perfomance_ganre"] a, .ui-selectgroup-list[aria-labelledby="ui-perfomance_theater"] a',
            function (event) {
                armdMkPerfomance.resetSearchForm();
                armdMkPerfomance.isSearch = false;
                armdMkPerfomance.loadList(false, false);

        });

        // search
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMkPerfomance.resetFilterForm();
                armdMkPerfomance.isSearch = true;
                armdMkPerfomance.loadList(true, false);
            }
        });

        // more button
        $('#perfomance-more-button').bind('click', function(event) {
            event.preventDefault();
            armdMkPerfomance.loadList(armdMkPerfomance.isSearch, true);
        });

        // sort
        $('#perfomance-sort-filter a').bind('click', function(event) {
            event.preventDefault();
            $('#perfomance-sort-filter a').removeClass('active');
            $(this).addClass('active');
            armdMkPerfomance.loadList(armdMkPerfomance.isSearch, false);
            return false;
        });

    },

    hideMore: function() {
        $('#perfomance-more-container').hide();
    },

    showMore: function() {
        $('#perfomance-more-container').show();
    },

    resetFilterForm: function() {
        $('#perfomance_ganre, #perfomance_theater').val('').selectgroup('refresh');
    },

    resetSearchForm: function() {
        $('#search-txt').val('');
    },

    loadList: function(isSearch, append) {
        armdMk.startLoading();
        var data = {
            'limit': armdMkPerfomance.loadByCount
        };

        if (append) {
            data['offset'] = $('#perfomance-container .plitka-one-wrap').length;
        } else {
            data['offset'] = 0;
        }

        if (isSearch) {
            data['search_query'] = $('#search-txt').val();
        } else {

            data['ganre_id'] = $('#perfomance_ganre').val();
                    	
        }

        data['sort_by'] = $('#perfomance-sort-filter a.active').data('sort-by');

        $.ajax({
            url: Routing.generate('armd_perfomance_list_content'),
            data: data,
            type: 'get',
            dataType: 'html',
            success: function(data) {
                if (append) {
                    $('#perfomance-container').append(data);
                } else {
                    $('#perfomance-container').html(data);
                }

                if ($(data).filter('.plitka-one-wrap').length < armdMkPerfomance.loadByCount) {
                    armdMkPerfomance.hideMore();
                } else {
                    armdMkPerfomance.showMore();
                }

                if ($.trim(data).length === 0 && !append) {
                    $('#perfomance-container').html('<h2>Не найдено</h2>');
                }
            },
            complete: function() {
                armdMk.stopLoading();
            }
        });
    }

};