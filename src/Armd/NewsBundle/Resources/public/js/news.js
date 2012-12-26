var armdMkNews = {
    categorySlug: '',

    init: function() {
        console.log('news init');
        $('#more-news').bind('click', function(event) {
            event.preventDefault();
            armdMkNews.loadMoreNews();
        });

        $('.search-dates-button').bind('click', function(event){
            event.preventDefault();
            armdMkNews.loadNewsByDates();
        });
    },


    loadMoreNews: function() {
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
                $('#first-date').html(firstLoadedDate);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Error loading news: ' + errorThrown);
            }
        })
    },

    loadNewsByDates: function() {
        var firstDate = $('#first-date').html();
        var lastDate = $('#last-date').html();
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
                $('#first-date').html(firstLoadedDate);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Error loading news: ' + errorThrown);
            }
        });
    },

    dateToIso8601: function(dateString) {
        return moment(dateString, 'MM.DD.YYYY').format('YYYY-MM-DD');
    },

    dateFromIso8601: function(dateString) {
        return moment(dateString, 'YYYY-MM-DD').format('MM.DD.YYYY');
    }

};