var armdMkLectures = {
    loadByCount: 32,
    isSearch: false,
    lectureSuperTypeCode: null,

    init: function(lectureSuperTypeCode) {
        armdMkLectures.lectureSuperTypeCode = lectureSuperTypeCode;

        // filter
        $('#lectures-filter-submit').bind('click', function(event) {
            event.preventDefault();
            armdMkLectures.resetSearchForm();
            armdMkLectures.isSearch = false;
            armdMkLectures.loadList(false, false);
        });

        // search
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMkLectures.resetFilterForm();
                armdMkLectures.isSearch = true;
                armdMkLectures.loadList(true, false);
            }
        });

        // more button
        $('#lecture-more-button').bind('click', function(event) {
            event.preventDefault();
            armdMkLectures.loadList(armdMkLectures.isSearch, true);
        });
    },

    hideMore: function() {
        $('#lecture-more-container').hide();
    },

    showMore: function() {
        $('#lecture-more-container').show();
    },

    resetFilterForm: function() {
        $('#lecture_category').val('').selectgroup('refresh');
    },

    resetSearchForm: function() {
        $('#search-txt').val('');
    },

    loadList: function(isSearch, append) {
        armdMk.startLoading();
        var data = {
            'limit': armdMkLectures.loadByCount
        };

        if (append) {
            data['offset'] = $('#lecture-container .plitka-one-wrap').length;
        } else {
            data['offset'] = 0;
        }

        if (isSearch) {
            data['search_query'] = $('#search-txt').val();
        } else {
            data['category_id'] = $('#lecture_category').val();
        }

        $.ajax({
            url: Routing.generate('armd_lecture_list', {'lectureSuperTypeCode': armdMkLectures.lectureSuperTypeCode}),
            data: data,
            type: 'get',
            dataType: 'html',
            success: function(data) {
                if (append) {
                    $('#lecture-container').append(data);
                } else {
                    $('#lecture-container').html(data);
                }

                if ($(data).find('.plitka-one-wrap').length < armdMkLectures.loadByCount) {
                    armdMkLectures.hideMore();
                } else {
                    armdMkLectures.showMore();
                }

                if ($.trim(data).length === 0) {
                    $('#lecture-container').html('<h2>Не найдено</h2>');
                }
            },
            complete: function() {
                armdMk.stopLoading();
            }
        });
    }
};