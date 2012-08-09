/* Author:

*/

$(function() {
  $(".tabs").tabs(".panes > .pane", { history: true });
  armdMk.initSearch();
});

var armdMk = {
    initSearch: function() {
        $('.search_bar input[type=image]').bind('click', function() {
            $(this).closest('form').submit();
        });

        $('.search_bar input[type=image]').bind('keypress', function() {
            if(e.which == 13) {
                $(this).closest('form').submit();
            }
        });
    }
};

