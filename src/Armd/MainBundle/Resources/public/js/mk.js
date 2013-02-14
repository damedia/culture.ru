var armdMk = {
    // must be set in base template
    baseUrl: null,
    locale: null,

    init: function() {
        armdMk.initSearch();
        armdMk.initTabs();
    },

    initSearch: function() {
        $('.search_bar input[type=image]').bind('click', function() {
            $(this).closest('form').submit();
        });

        $('.search_bar input[type=image]').bind('keypress', function() {
            if(e.which == 13) {
                $(this).closest('form').submit();
            }
        });
    },

    initTabs: function() {
        $(".tabs").tabs(".panes > .pane", { history: true });
    },

    asset: function(path) {
        return armdMk.baseUrl + path;
    },

    startLoading: function() {
        armdMk.startLoadingBlock();
    },

    stopLoading: function() {
        armdMk.stopLoadingBlock();
    },

    startLoadingBlock: function(blockSelector) {
        if (typeof(blockSelector) === 'undefined') {
            blockSelector = 'body';
        }
        $(blockSelector)
            .append($('<div class="loading-block"></div>'));
    },

    stopLoadingBlock: function(blockSelector) {
        if (typeof(blockSelector) === 'undefined') {
            blockSelector = 'body';
        }
        $(blockSelector).children('.loading-block').remove();
    }
};