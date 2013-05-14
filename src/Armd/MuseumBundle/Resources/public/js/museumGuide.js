var armdMuseumGuide = {

    init: function () {
        // filter
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-filter-city"] a, .ui-selectgroup-list[aria-labelledby="ui-filter-museum"] a',
            function (event) {
                $('#museums-filter-form').submit();
            });
        // pdf
        $(".pdf-view-trigger").fancybox({
                fitToView : false,
                width : '95%',
                height : '95%',
                autoSize : false,
                closeClick : false,
                openEffect : 'none',
                closeEffect : 'none'
            });
    }
};


