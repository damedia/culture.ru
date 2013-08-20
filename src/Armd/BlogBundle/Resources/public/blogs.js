$(document).ready(function () {

    $('body').on('click', '.popular-next', function (e) {
        e.preventDefault();
        var caller = $(this),
            container = $('.popular-container'),
            page = caller.data('page') + 1,
            url = caller.attr('href'),
            request = {
                'page': page
            };

        $.commonAJAX.processData(url, request, function (response) {
            if (response.isSuccessful) {
                container.html(response.html);
            }

            if (response.finish) {
                container.find('.popular-next').remove();
            }


        }, false, true);
    });

    $('body').on('click', '.load-more-links', function (e) {
        e.preventDefault();
        var caller = $(this),
            container = $('.b-post-list'),
            page = caller.data('page') + 1,
            user = caller.data('user'),
            url = caller.attr('href'),
            request = {
                'page': page
            };

        if (user !== '') {
            url += '/' + user;
        }

        $.commonAJAX.processData(url, request, function (response) {
            if (response.isSuccessful) {
                container.append(response.html);
                caller.data('page', page);
            }

            if (response.finish) {
                caller.remove();
            }
        }, false, true);
    });
});