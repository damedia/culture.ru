$(document).ready(function(){
    var gotoButton = $('<div class="goto-button"><a target="_blank" href="#">Перейти на страницу</a></div>'),
        gotoButtonPlaceholder = $('.sonata-bc .navbar-text', document),
        gotoButtonUrl = AdminEntitiesUrlMapper.resolveCurrentUrl();

    if (gotoButtonUrl) {
        gotoButton.find('a').attr('href', gotoButtonUrl);
        gotoButtonPlaceholder.prepend(gotoButton);
    }
});