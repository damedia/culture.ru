$(document).ready(function() {
    var sonataForm = $('div.sonata-ba-form'),
        sonataFormFieldset = $('fieldset', sonataForm),
        sonataFormButtons = $('div.form-actions input[type="submit"], div.form-actions a', sonataForm),
        sonataFormTemplateSelect = $('select.DamediaSpecialProjectBundle_templateSelect', sonataFormFieldset),

        pageEssentials = $(':hidden[name="admin_createPage_ajax"]'),
        loaderImgSrc = pageEssentials.attr('data-loaderImageSrc'),
        ajaxRequestScript = pageEssentials.attr('data-getTemplateBlocksPath'),

        ajaxResponseDiv = $('<div class="createPage_ajaxResponse"></div>'),

        createModalLayer = function(){
            var div = $('<div id="modal-layer"><img src="'+loaderImgSrc+'" /></div>'),
                width = $(document).width(),
                height = $(document).height();

            div.css({'width': width, 'height': height}).appendTo('body', document);
        },
        removeModalLayer = function(){
            $('#modal-layer').remove();
        },
        loadBlocks = function(value){
            if (value === '') {
                ajaxResponseDiv.html('');
                return;
            }

            createModalLayer();

            $.post(ajaxRequestScript, { 'templateId': value }, function(data){
                ajaxResponseDiv.html(data.content);
                removeModalLayer();
            });
        };

    ajaxResponseDiv.appendTo(sonataFormFieldset);

    loadBlocks(sonataFormTemplateSelect.val());
    sonataFormTemplateSelect.on('change', function(){
        loadBlocks($(this).val());
    });

    sonataFormButtons.on('click', function(event){
        var element = $(this);

        event.preventDefault();

        alert('Check console, bro!');
        console.log(element);
    });
});