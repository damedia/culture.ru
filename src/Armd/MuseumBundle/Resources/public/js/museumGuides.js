var armdMuseumGuides = {

    init: function () {
        armdMuseumGuides.initUi();
    },

    initUi: function () {
        $('#museums-filter-form').ajaxForm({
            beforeSubmit: function(){
                armdMk.startLoading();
                armdMuseumGuides.resetTextFilter();
                armdMuseumGuides.startLoading();
            },
            success: function(data) {
                $('#museums-container').html(data);
                armdMuseumGuides.stopLoading();
                armdMuseumGuides.initLoadedUi();
                armdMk.stopLoading();
            }
        });

        // init search
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMuseumGuides.loadTextFilterResult();
            }
        });
    },

    initLoadedUi: function() {
        $('.museum-image, .plitka-name').on('click', $(this), function(){
            $(this).closest('.plitka-one-wrap').toggleClass('plitka-one-wrap-opened');
        });

        if ($('.vob').length) {
            $('.vob').fancybox({
                'autoScale': false,
                'autoDimensions': false,
                'padding': 0,
                'margin': 0,
                'fitToView': true
            });
        }

        $(".iframe").fancybox({
            'width': '100%',
            'height': '100%',
            'autoScale': true,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'
        });
    },

    resetFilter: function() {
        $('#filter-region').val('').selectgroup('refresh');
        $('#filter-category').val('').selectgroup('refresh');
    },

    resetTextFilter: function() {
        $('#search-txt').val('').selectgroup('refresh');
    },

    startLoading: function () {
        $('#museums-loading').show();
    },

    stopLoading: function () {
        $('#museums-loading').hide();
    },

    loadTextFilterResult: function() {
        var searchQuery = $('#search-txt').val().trim();
        if (searchQuery.length > 0) {
            armdMuseums.resetFilter();
            armdMk.startLoading();
            $.ajax({
                url: Routing.generate('armd_museum_guides_list'),
                data: {
                    'search_query': searchQuery
                },
                dataType: 'html',
                method: 'get',
                success: function(data) {
                    $('#museums-container').html(data);
                },
                complete: function() {
                    armdMk.stopLoading();
                }
            });
        }
    }

};
