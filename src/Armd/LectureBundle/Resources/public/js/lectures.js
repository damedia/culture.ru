var armdMkLectures = {
    loadByCount: 32,
    isSearch: false,
    lectureSuperTypeCode: null,

    init: function(lectureSuperTypeCode) {
        armdMkLectures.lectureSuperTypeCode = lectureSuperTypeCode;

        // filter
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-lecture_category"] a, .ui-selectgroup-list[aria-labelledby="ui-lecture_sub_category"] a',
            function (event) {
                armdMkLectures.resetSearchForm();
                armdMkLectures.isSearch = false;
                if ($(event.target).closest('.ui-selectgroup-list').attr('aria-labelledby') === 'ui-lecture_category') {
                    armdMkLectures.loadSubCategories();
                }
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

        // sort
        $('#sort-list a').bind('click', function(event) {
            event.preventDefault();
            $('#sort-list li').removeClass('active');
            $(this).closest('li').addClass('active');
            armdMkLectures.loadList(armdMkLectures.isSearch, false);
        });

        // sort by region
        $('#lecture-container').on('click', '.cinema-category-link', function(event) {
            event.preventDefault();
            var categoryId = $(this).data('category-id');

            // get right select
            var select = $('#lecture_sub_category');
            if (select.length === 0) {
                select = $('#lecture_category');
            }

            // add value if not exists
            if (select.find('option[value=' + categoryId + ']').length === 0) {
                select.append('<option value="' + categoryId + '">' + $(this).text() + '</option>');
            }
            select.val(categoryId).selectgroup('refresh');
            armdMkLectures.loadList(false, false);
        });
    },

    hideMore: function() {
        $('#lecture-more-container').hide();
    },

    showMore: function() {
        $('#lecture-more-container').show();
    },

    resetFilterForm: function() {
        $('#lecture_category, #lecture_sub_category').val('').selectgroup('refresh');
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
            var categoryId = $('#lecture_category').val();
            var subCategoryId = $('#lecture_sub_category').val();
            data['category_id'] = parseInt(subCategoryId) > 0 ? subCategoryId : categoryId;

            if ($('#lecture_category option:selected').data('system-slug') === 'CINEMA_TOP_100') {
                data['cinema_top100'] = 1;
            }
        }

        data['sort_by'] = $('#sort-list li.active a').data('sort-by');

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

                if ($(data).filter('.plitka-one-wrap').length < armdMkLectures.loadByCount) {
                    armdMkLectures.hideMore();
                } else {
                    armdMkLectures.showMore();
                }

                if ($.trim(data).length === 0 && !append) {
                    $('#lecture-container').html('<h2>Не найдено</h2>');
                }
            },
            complete: function() {
                armdMk.stopLoading();
            }
        });
    },

    loadSubCategories: function () {
        var select = $('#lecture_sub_category');
        if (select.length) {
            var parentCategoryId = $('#lecture_category').val();
            select.html('<option value="0">Все</option>');
            if (parentCategoryId > 0) {
                armdMk.startLoading();
                $.ajax({
                    url: Routing.generate(
                        'armd_lecture_categories',
                        {'lectureSuperTypeCode': armdMkLectures.lectureSuperTypeCode, 'parentId': parentCategoryId}
                    ),
                    dataType: 'json',
                    success: function (data) {
                        for (var i in data) {
                            select.append($('<option>', {value: data[i].id}).text(data[i].title));
                        }
                        select.selectgroup('refresh');
                    },
                    complete: function () {
                        armdMk.stopLoading();
                    }
                });
            }
        }
    }
};