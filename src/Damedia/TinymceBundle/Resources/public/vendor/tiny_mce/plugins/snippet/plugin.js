tinymce.PluginManager.add("snippet", function(editor, url) {
    /**
     * console.log(editor.settings) is the placeholder for passed parameters
     * in Damedia plugins we are using 'editor.settings.parameters'
     */

    function createSnippet(){
        var modalWindowDiv = $('#snippetBootstrapModal'),
            entitySelect = $('select[name="entity"]', modalWindowDiv),
            viewSelect = $('select[name="view"]', modalWindowDiv),
            autocompleteInput = $('input[name="object"]', modalWindowDiv),
            selectedEntity,
            selectedView,
            modalLayerDivId = 'modal-layer',
            modalLayerImgSrc = editor.settings.parameters.modalLayerImgSrc,
            setEntity = function(){
                var entityTitle,
                    entityTitleSpan = $('span[name="entityTitle"]', modalWindowDiv);

                entityTitle = entitySelect.find('option:selected').text();
                entityTitleSpan.text(entityTitle);
                selectedEntity = entitySelect.val();
                selectedView = viewSelect.val();
                autocompleteInput.val('');
                appendSelectedEntityViewsOptions();
                setView();
            },
            setView = function(){
                selectedView = viewSelect.val();
            },
            appendSelectedEntityViewsOptions = function(){
                $.ajax(constructAjaxUrl(editor.settings.parameters.entityViewsListUrl, selectedEntity), {
                    async: false,
                    beforeSend: function(){
                        createModalLayer();
                    },
                    complete: function(){
                        removeModalLayer();
                    },
                    success: function(data){
                        $('option', viewSelect).remove();

                        $.each(data, function(key, val){
                            $('<option value="' + key + '" data-twig="' + val.twig + '">' + val.title + '</option>').appendTo(viewSelect);
                        });
                    }
                });
            },
            constructAjaxUrl = function(resource, entity, searchTerm){
                var url = resource + '?_sonata_admin=' + editor.settings.parameters.sonataAdmin;

                if (entity) {
                    url += '&entity=' + entity;
                }
                if (searchTerm) {
                    url += '&q=' + searchTerm;
                }

                return url;
            },
            createModalLayer = function(){
                var div = $('<div id="' + modalLayerDivId + '"><img src="' + modalLayerImgSrc + '" /></div>'),
                    width = $(document).width(),
                    height = $(document).height();

                if ($('#'+modalLayerDivId).length === 0) {
                    div.css({'width': width, 'height': height}).appendTo("body", document);
                }
            },
            removeModalLayer = function(){
                $("#"+modalLayerDivId).remove();
            },
            getSearchResults = function(request, response){
                $.ajax(constructAjaxUrl(editor.settings.parameters.acAjaxUrl, selectedEntity, request.term), {
                    beforeSend: function(){
                        createModalLayer();
                    },
                    complete: function(){
                        removeModalLayer();
                    },
                    success: function(data){
                        response(data);
                    }
                });
            },
            insertSnippet = function(label, value){
                var snippetLabel,
                    snippetTwig,
                    snippetHtml;

                snippetLabel = 'Type: ' + selectedEntity + ', ID: ' + value + ', Label: ' + label + ', View: ' + selectedView;
                snippetTwig = '{% render url(\'damedia_foreign_entity\', { \'entity\': \'' + selectedEntity + '\', \'itemId\': ' + value + ', \'view\': \'' + selectedView + '\' }) %}';
                snippetHtml = '<input type="button" class="snippet" value="' + snippetLabel + '" data-twig="' + snippetTwig + '" disabled="disabled" /> ';

                editor.execCommand('mceInsertContent', false, snippetHtml);

                modalWindowDiv.modal('hide');
            };

        modalWindowDiv.on('show', function(){
            setEntity();
        });

        entitySelect.on('change', function(){
            setEntity();
        });

        viewSelect.on('change', function(){
            setView();
        });

        //using 'unbind()' here is inevitable else 'autocompleteselect' callback will be binded many times!
        autocompleteInput.autocomplete({ source: getSearchResults }).unbind('autocompleteselect').on('autocompleteselect', function(event, ui){
            insertSnippet(ui.item.label, ui.item.value);
        });

        modalWindowDiv.modal();
    }

	editor.addButton('snippetAdd', {
		text: 'Snippet',
		toolbar: 'snippet',
		icon: false,
		onclick: createSnippet
	});
});