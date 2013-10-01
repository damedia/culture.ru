$(document).ready(function(){
    var gotoButton = $('<div class="goto-button"><a href="#">Go to page</a></div>'),
        gotoButtonPlaceholder = $('.sonata-bc .navbar-text', document);

    gotoButtonPlaceholder.prepend(gotoButton);
});