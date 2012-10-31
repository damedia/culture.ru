var armdMuseums = {
    inputTimeout: null,

    init: function () {
        $.manageAjax.create('lifo', {queue: 'clear', maxRequests: 2, abortOld: true});

        armdMuseums.initUi();
    },

    initUi: function () {
        // search
        armdMuseums.afterDelayedEvent('keyup', '#search_text', 1000, function (event) {
            var c = String.fromCharCode(event.keyCode);
            var isWordCharacter = c.match(/\w/);
            var isBackspaceOrDelete = (event.keyCode == 8 || event.keyCode == 46);
            if (isWordCharacter || isBackspaceOrDelete) {
                armdMuseums.loadList();
            }
        });

        $('form.obr-form').bind('submit', function (event) {
            event.preventDefault();
            armdMuseums.loadList();

        });

        $('#search_text').bind('focus', function () {
            if ($(this).val() === 'Поиск по музеям') {
                $(this).val('');
            }
        });

        $('#search_text').bind('blur', function () {
            if ($(this).val().length == 0) {
                $(this).val('Поиск по музеям');
            }
        });

        $('.obr-submit-input').bind('click', function () {
            armdMuseums.loadList();
        });

        armdMuseums.initLoadedUi();
    },

    initLoadedUi: function() {
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
    },

    afterDelayedEvent: function afterDelayedEvent(eventtype, selector, delay, action) {
        $(selector).bind(eventtype, function (event) {
            if (typeof(armdMuseums.inputTimeout) != "undefined") {
                clearTimeout(armdMuseums.inputTimeout);
            }
            armdMuseums.inputTimeout = setTimeout(function () {
                action.call(this, event);
            }, delay);
        });
    },

    loadList: function () {
        armdMuseums.startLoading();
        $.manageAjax.clear('lifo', true);
        $.manageAjax.add('lifo', {
            url: Routing.generate('armd_museum_list'),
            cache: false,
            dataType: 'html',
            type: 'POST',
            data: {
                searchString: $('#search_text').val()
            },
            success: function (data) {
                $('#museums-container').html(data);
                armdMuseums.stopLoading();
            }
        });
    }
}
