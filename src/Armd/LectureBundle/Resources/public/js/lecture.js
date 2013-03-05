var armdMkLecture = {
    lectureSuperTypeCode: null,

    init: function(lectureSuperTypeCode) {
        armdMkLecture.lectureSuperTypeCode = lectureSuperTypeCode;
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                window.location = Routing.generate('armd_lecture_default_index', {
                    'lectureSuperTypeCode': armdMkLecture.lectureSuperTypeCode,
                    'search_query': $('#search-txt').val()
                })
            }
        });
    }
};