tinymce.PluginManager.add("snippet", function(editor, url) {
    /**
     * console.log(editor.settings) is the placeholder for passed parameters
     * in Damedia plugins we are using 'editor.settings.parameters'
     */

    function createSnippet(){
        var modalWindowDiv = $('#snippetBootstrapModal'),
            entitySelect = $('select[name="entity"]', modalWindowDiv),
            autocompleteInput = $('input[name="object"]', modalWindowDiv),
            entity,
            setEntity = function(){
                var entityTitle,
                    entityTitleSpan = $('span[name="entityTitle"]', modalWindowDiv);

                entityTitle = entitySelect.find('option:selected').text();
                entityTitleSpan.text(entityTitle);
                entity = entitySelect.val();
                autocompleteInput.val('');
            },
            constructAjaxUrl = function(entity, searchTerm){
                return editor.settings.parameters.acAjaxUrl + '?_sonata_admin=' + editor.settings.parameters.sonataAdmin + '&entity=' + entity + '&q=' + searchTerm;
            },
            getSearchResults = function(request, response){
                $.ajax(constructAjaxUrl(entity, request.term), {
                    success: function(data){
                        if (data.length) {
                            autocompleteInput.removeClass("ui-state-error");
                        }
                        else {
                            autocompleteInput.addClass("ui-state-error");
                        }

                        response(data);
                    },
                    error: function(xhr, err){
                        autocompleteInput.addClass("ui-state-error");
                    }
                });
            },
            insertSnippet = function(label, value){
                var snippet = { entityId: value,
                                label: label,
                                type: entity,
                                viewId: null
                              },
                    snippetValue,
                    snippetTwig,
                    snippetHtml;

                snippetValue = 'Type: ' + snippet.type + ', ID: ' + snippet.entityId + ', Label: ' + snippet.label;
                snippetTwig = '{% render url(\'damedia_foreign_entity\', { \'entity\': \'' + snippet.type + '\', \'itemId\': ' + snippet.entityId + ' }) %}';
                snippetHtml = '<input type="button" class="snippet" value="' + snippetValue + '" data-twig="' + snippetTwig + '" disabled="disabled" /> ';

                editor.execCommand('mceInsertContent', false, snippetHtml);

                modalWindowDiv.modal('hide');
            };

        modalWindowDiv.on('show', function(){
            setEntity();
        });

        entitySelect.on('change', function(){
            setEntity();
        });

        //using 'unbind()' is inevitable here else 'autocompleteselect' callback will be binded many times!
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