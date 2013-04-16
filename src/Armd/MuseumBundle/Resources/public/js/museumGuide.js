var armdMuseumGuide = {

    init: function () {
        // filter
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-filter-city"] a, .ui-selectgroup-list[aria-labelledby="ui-filter-museum"] a',
            function (event) {
                armdMuseumGuide.resetSearchForm();
                armdMuseumGuide.isSearch = false;
                if ($(event.target).closest('.ui-selectgroup-list').attr('aria-labelledby') === 'ui-lecture_category') {
                    armdMuseumGuide.loadSubCategories();
                }
                armdMuseumGuide.loadList(false, false);

            });

        // pdf-viewer
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
