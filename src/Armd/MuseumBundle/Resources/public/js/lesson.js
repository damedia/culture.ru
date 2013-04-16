var armdMkLesson = {
    isSearch: false,

    init: function(count) {

    	armdMkLesson.loadByCount = count;
    	
    	armdMkLesson.isSearch = $('#search-txt').val() ? true : false;
    	
        // filter
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-lesson_museum"] a, .ui-selectgroup-list[aria-labelledby="ui-lesson_city"] a, .ui-selectgroup-list[aria-labelledby="ui-lesson_education"] a, .ui-selectgroup-list[aria-labelledby="ui-lesson_subject"] a, .ui-selectgroup-list[aria-labelledby="ui-lesson_skill"] a',
            function (event) {
                armdMkLesson.resetSearchForm();
                armdMkLesson.isSearch = false;
                armdMkLesson.loadList(false, false);

        });

        // search
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMkLesson.resetFilterForm();
                armdMkLesson.isSearch = true;
                armdMkLesson.loadList(true, false);
            }
        });

        // more button
        $('#lesson-more-button').bind('click', function(event) {
            event.preventDefault();
            armdMkLesson.loadList(armdMkLesson.isSearch, true);
        });

    },

    hideMore: function() {
        $('#lesson-more-container').hide();
    },

    showMore: function() {
        $('#lesson-more-container').show();
    },

    resetFilterForm: function() {
        $('#lesson_museum, #lesson_city, #lesson_subject, #lesson_education, #lesson_skill').val('').selectgroup('refresh');
    },

    resetSearchForm: function() {
        $('#search-txt').val('');
    },

    loadList: function(isSearch, append) {
        armdMk.startLoading();
        var data = {
            'limit': armdMkLesson.loadByCount
        };

        if (append) {
            data['offset'] = $('#museum-education .museum-education-list li').length;
        } else {
            data['offset'] = 0;
        }

        if (isSearch) {
            data['search_query'] = $('#search-txt').val();
        } else {
			
        	data['lesson_museum'] = $('#lesson_museum').val();
        	data['lesson_city'] = $('#lesson_city').val();
        	data['lesson_education'] = $('#lesson_education').val();
        	data['lesson_subject'] = $('#lesson_subject').val();
			data['lesson_skill'] = $('#lesson_skill').val();
			
        }

        $.ajax({
            url: Routing.generate('armd_lesson_list_content'),
            data: data,
            type: 'get',
            dataType: 'html',
            success: function(data) {
                if (append) {
                    $('#museum-education .museum-education-list').append(data);
                } else {
                    $('#museum-education .museum-education-list').html(data);
                }

                if ($(data).filter('li').length < armdMkLesson.loadByCount) {
                    armdMkLesson.hideMore();
                } else {
                    armdMkLesson.showMore();
                }

                if ($.trim(data).length === 0 && !append) {
                    $('#museum-education .museum-education-list').html('<li><h2>Не найдено</h2></li>');
                }
            },
            complete: function() {
                armdMk.stopLoading();
            }
        });
    }

};