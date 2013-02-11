var armdMuseums = {

    init: function () {
        armdMuseums.initUi();
    },

    initUi: function () {
        $('#museums-filter-form').ajaxForm({
            beforeSubmit: function(){
                armdMuseums.startLoading();
            },
            success: function(data) {
                $('#museums-container').html(data);
                armdMuseums.stopLoading();
                armdMuseums.initLoadedUi();
            }
        });
    },

    initLoadedUi: function() {
        $('.museum-image, .plitka-name').on('click', $(this), function(){
            $(this).closest('.plitka-one-wrap').toggleClass('plitka-one-wrap-opened');
        })

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

    startLoading: function () {
        $('#museums-loading').show();
    },

    stopLoading: function () {
        $('#museums-loading').hide();
    }

};
