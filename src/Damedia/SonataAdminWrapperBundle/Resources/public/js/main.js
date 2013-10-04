$(document).ready(function(){
    var sonataBottomButtons = $('.sonata-bc .form-actions', document),
        previewBottomSidebar = $('<div class="buttons-sidebar" style=""></div>'),
        button_sitePreview,
        gotoButtonUrl = AdminEntitiesUrlMapper.resolveCurrentUrl();

    sonataBottomButtons.prepend(previewBottomSidebar);

    if (gotoButtonUrl) {
        button_sitePreview = $('<div class="goto-button"><a target="_blank" href="' + gotoButtonUrl + '">Показать на сайте</a></div>');
        previewBottomSidebar.append(button_sitePreview);
    }
});