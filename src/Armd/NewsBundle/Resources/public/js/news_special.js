var armdMkNews = {
    loadByCount: 30,
    loadByCountMore: 4,

    init: function() {

        $('.show-more').bind('click', function(event) {
            event.preventDefault();
            armdMkNews.loadMoreNews($(this));
        });

        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMkNews.loadSearchResult(false);
                armdMkNews.initMoreButtonToLoadSearch();
            }
        });
    },


    initMoreButtonToLoadSearch: function() {
        $('.more').show();
        $('#show-more').unbind('click');
        $('#show-more').bind('click', function (event) {
            event.preventDefault();
            armdMkNews.loadSearchResult(true);
        });

    },

    /**
     * uses global var firstLoadedDatet
     */
    loadMoreNews: function(a) {
        armdMk.startLoading();
        var category = $('#tabs-selector').length ? $('#tabs-selector li.active a').attr('slug') : 'news',
        	offset = $('#' + category + 'tab article.event-anons').length;
                
        $.ajax({
            url: Routing.generate('armd_main_special_news_more_load'),
            data : {
                category: category,
                limit: armdMkNews.loadByCountMore,
                offset: offset            	
            },
            type: 'get',
            dataType: 'html',

            success: function(data) {
            	if (data != '') {
	                $('#' + category + 'tab article.event-anons:last').after(data);
	                offset = $('#' + category + 'tab article.event-anons').length;
            	}
            	else {
            		$(a).hide();
            	}
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
        var category = $('#tabs-selector li.active a').attr('slug');

        if (append) {
            offset = $('#' + category + 'tab article.event-anons').length;
        } else {
            offset = 0;
        }

        data = {
            offset: offset,
            limit: armdMkNews.loadByCount,
            search_query: searchQuery,
            category_slug: category
        };

        armdMk.startLoading();
        $.ajax({
            url: Routing.generate('armd_main_special_newstext_search_result'),
            data: data,
            cache: false,
            type: 'get',
            dataType: 'html',
            success: function(data) {
                if (append) {
                    $('#' + category + 'tab').append(data);
                } else {
                    $('#featured').hide();
                    $('#' + category + 'tab').html(data);
                }
                if ($.trim(data) === '' || $(data).filter('article.news-search-result-item').length < armdMkNews.loadByCount) {
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