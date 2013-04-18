var armdMkLecture = {
    lectureSuperTypeCode: null,

    init: function(lectureSuperTypeCode) {
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-lecture_category"] a, .ui-selectgroup-list[aria-labelledby="ui-lecture_sub_category"] a', 
            function (event) {
                if ($(event.target).closest('.ui-selectgroup-list').attr('aria-labelledby') === 'ui-lecture_category') {
                    if($('#lecture_sub_category').length) {
                        armdMkLecture.loadSubCategories();
                    } else {
                        $('#lectures-filter').submit();
                    }
                }
                if($(event.target).closest('.ui-selectgroup-list').attr('aria-labelledby') === 'ui-lecture_sub_category') {
                    $('#lectures-filter').submit();
                }
            });

        armdMkLecture.lectureSuperTypeCode = lectureSuperTypeCode;
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                window.location = Routing.generate('armd_lecture_default_index', {
                    'lectureSuperTypeCode': armdMkLecture.lectureSuperTypeCode,
                    'search_query': $('#search-txt').val()
                });
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
                        {'lectureSuperTypeCode': armdMkLecture.lectureSuperTypeCode, 'parentId': parentCategoryId}
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