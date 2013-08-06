$(document).ready(function () {

    $('body').on('click', '.popular-next', function (e) {
        e.preventDefault();

        console.log('loading next');
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
});