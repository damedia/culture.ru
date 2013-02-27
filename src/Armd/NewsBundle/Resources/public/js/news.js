var armdMkNews = {
    categorySlug: '',
    loadByCount: 30,

    init: function(categorySlug) {
        armdMkNews.categorySlug = categorySlug;

        $('#more-news').bind('click', function(event) {
            event.preventDefault();
            armdMkNews.loadMoreNews();
        });

        $('.search-dates-button').bind('click', function(event){
            event.preventDefault();
            armdMkNews.loadNewsByDates();
            armdMkNews.initMoreButtonToLoadByDates();
        });

        $('#first-date').html(armdMkNews.dateFromIso8601(firstLoadedDate));
        $('#last-date').html(moment().format('DD.MM.YYYY'));

        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMkNews.loadSearchResult(false);
                armdMkNews.initMoreButtonToLoadSearch();
            }
        });
    },

    initMoreButtonToLoadByDates: function() {
        $('.more').show();
        $('#more-news').unbind('click');
        $('#more-news').bind('click', function (event) {
            event.preventDefault();
            armdMkNews.loadMoreNews();
        });
    },

    initMoreButtonToLoadSearch: function() {
        $('.more').show();
        $('#more-news').unbind('click');
        $('#more-news').bind('click', function (event) {
            event.preventDefault();
            armdMkNews.loadSearchResult(true);
        });

    },

    /**
     * uses global var firstLoadedDatet
     */
    loadMoreNews: function() {
        armdMk.startLoading();
        $.ajax({
            url: Routing.generate('armd_news_two_column_list', {
                category: armdMkNews.categorySlug
            }),
            type: 'get',
            data: {
                first_loaded_date: firstLoadedDate
            },
            dataType: 'html',

            success: function(data) {
                $('#news-container').append(data);
                $('#first-date').html(armdMkNews.dateFromIso8601(firstLoadedDate));
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Error loading news: ' + errorThrown);
            },
            complete: function() {
                armdMk.stopLoading();
            }
        })
    },

    loadSearchResult: function(append) {
        var offset;
        var searchQuery = $('#search-txt').val();
        var data;

        if (append) {
            offset = $('#news-container article.news-search-result-item').length;
        } else {
            offset = 0;
        }

        data = {
            offset: offset,
            limit: armdMkNews.loadByCount,
            search_query: searchQuery,
            category_slug: armdMkNews.categorySlug
        };

        armdMk.startLoading();
        $.ajax({
            url: Routing.generate('armd_news_text_search_result'),
            data: data,
            cache: false,
            type: 'get',
            dataType: 'html',
            success: function(data) {
                if (append) {
                    $('#news-container').append(data);
                } else {
                    $('#featured').hide();
                    $('#news-container').html(data);
                }
                if ($.trim(data) === '' || $(data).find('article.news-search-result-item').length < armdMkNews.loadByCount) {
                    $('.more').hide();
                }
            },
            complete: function() {
                armdMk.stopLoading();
            }
        });
    },

    /**
     *  uses global var firstLoadedDate
     */
    loadNewsByDates: function() {
        var firstDate = armdMkNews.dateToIso8601($('#first-date').html());
        var lastDate = armdMkNews.dateToIso8601($('#last-date').html());
        $.ajax({
            url: Routing.generate('armd_news_two_column_list', {
                category: armdMkNews.categorySlug
            }),
            type: 'get',
            data: {
                'to_date': lastDate,
                'from_date': firstDate
            },
            dataType: 'html',

            success: function(data) {
                $('#news-container').html(data);
//                $('#first-date').html(armdMkNews.dateFromIso8601(firstLoadedDate));
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Error loading news: ' + errorThrown);
            }
        });
    },

    dateToIso8601: function(dateString) {
        return moment(dateString, 'DD.MM.YYYY').format('YYYY-MM-DD');
    },

    dateFromIso8601: function(dateString) {
        return moment(dateString, 'YYYY-MM-DD').format('DD.MM.YYYY');
    }

};