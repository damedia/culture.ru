var armdMkLectures = {
    loadByCount: 32,
    isSearch: false,
    lectureSuperTypeCode: null,

    init: function(lectureSuperTypeCode) {
        armdMkLectures.lectureSuperTypeCode = lectureSuperTypeCode;

        // filter
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-lecture_genre"] a',
            function (event) {
                armdMkLectures.resetSearchForm();
                armdMkLectures.isSearch = false;
                armdMkLectures.loadList(false, false);
                window.history.pushState(null, '', armdMkLectures.modifyQueryString({'genre_id': $('#lecture_genre').val()}));
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
        $('#sort-filter a').bind('click', function(event) {
            event.preventDefault();
            $('#sort-filter li').removeClass('active');
            $(this).closest('li').addClass('active');
            armdMkLectures.loadList(armdMkLectures.isSearch, false);
            window.history.pushState(null, '', armdMkLectures.modifyQueryString({'sort_by': $(this).data()['sortBy']}));
        });

        // alphabet filter
        $('#alphabet-filter a').on('click', function(event) {
            event.preventDefault();
            var li = $(this).closest('li');

            if (!li.hasClass('active')) {
                $('#alphabet-filter li').removeClass('active');
                li.addClass('active');
                armdMkLectures.loadList(false, false);
                window.history.pushState(null, '', armdMkLectures.modifyQueryString({'first_letter': $(this).data()['letter']}));
            }
        });

        // category filter when clicking on tile
        $('#lecture-container').on('click', '.cinema-genre-link', function(event) {
            $('#lecture_genre').val($(this).data('genre-id')).selectgroup('refresh');
            armdMkLectures.loadList(false, false);
        });
    },

    hideMore: function() {
        $('#lecture-more-container').hide();
    },

    showMore: function() {
        $('#lecture-more-container').show();
    },

    hideSortPanel: function() {
        $('#sort-panel').hide();
    },

    showSortPanel: function() {
        $('#sort-panel').show();
    },

    hideTagFilterPanel: function() {
        $('#tag-filter-panel').hide();
    },

    showTagFilterPanel: function() {
        $('#tag-filter-panel').show();
    },

    resetFilterForm: function() {
        $('#lecture_genre').val('').selectgroup('refresh');
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

        var genre1Id = $('#genre1_id').val();
        if (genre1Id > 0) {
            data['genre1_id'] = genre1Id;
        }

        data['genre_ids'] = [];
        if ($('#genre1_id').length) {
            data['genre_ids'].push($('#genre1_id').val());
        }


        if (isSearch) {
            armdMkLectures.hideSortPanel();
            armdMkLectures.hideTagFilterPanel();
            data['search_query'] = $('#search-txt').val();
        } else {
            armdMkLectures.showSortPanel();
            armdMkLectures.hideTagFilterPanel();

            var genreId = $('#lecture_genre').val();
            if (genreId > 0) {
                data['genre_ids'].push(genreId);
            }

            var firstLetter = $('#alphabet-filter li.active a').data('letter');
            if (firstLetter) {
                data['first_letter'] = firstLetter;
            }
        }

        data['sort_by'] = $('#sort-filter li.active a').data('sort-by');

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

                if ($.trim(data).length === 0 && !append) {
                    $('#lecture-container').html('<h2>Не найдено</h2>');
                }
            },

            complete: function() {
                armdMk.stopLoading();
            }
        });
    },

    parseParams: function(query) {
        var re = /([^&=]+)=?([^&]*)/g;
        var decodeRE = /\+/g; // Regex for replacing addition symbol with a space
        var decode = function (str) {return decodeURIComponent( str.replace(decodeRE, " ") );};
        var params = {}, e;
        while (e = re.exec(query)) {
            var k = decode( e[1] ), v = decode( e[2] );
            if (k.substring(k.length - 2) === '[]') {
                k = k.substring(0, k.length - 2);
                (params[k] || (params[k] = [])).push(v);
            }
            else params[k] = v;
        }
        return params;
    },
    modifyQueryString: function(params) {
        var vars = armdMkLectures.parseParams(window.location.search.replace('?', ''));
        $.each(params, function(k, v){ vars[k] = v; });
        return '?' + $.param(vars);
    },
};