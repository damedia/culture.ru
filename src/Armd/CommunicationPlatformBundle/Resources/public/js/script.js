/* Author:

*/

$(function() {
    //$(".tabs").tabs(".panes > .pane", { history: true });

    $('#auth-toggle').click(function(){
        $('#overlay').fadeIn();
        $('#auth-block').fadeToggle();
        return false;
    });
    $('#overlay').click(function(){
        $('#overlay').fadeOut();
        $('#auth-block').fadeOut();
    });

});
