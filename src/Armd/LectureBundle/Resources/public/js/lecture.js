var armdMkLecture = {
    lectureSuperTypeCode: null,

    init: function(lectureSuperTypeCode) {
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-lecture_genre"] a', function(event){
            $('#lectures-filter').submit();
        });

        armdMkLecture.lectureSuperTypeCode = lectureSuperTypeCode;

        $('#search-form').bind('submit', function(event){
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                window.location = Routing.generate('armd_lecture_cinema_index', { 'search_query': $('#search-txt').val() });
            }
        });

        $('.genre-link').on('click', function(event){
            event.preventDefault();
            $('#lecture_genre').val($(this).data('genre-id')).selectgroup('refresh');
            $('#lectures-filter').submit();
        });
    }
};