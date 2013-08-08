tinymce.PluginManager.add("snippet", function(editor, url) {
    /**
     * console.log(editor.settings) is the placeholder for passed parameters
     * in Damedia plugins we are using 'editor.settings.parameters'
     */

	var CONTENT_TYPES =	editor.settings.parameters.entityTypes || [{ value: 'news', text: 'Новость' }];

	function createSnippet(){
        var selectedType = CONTENT_TYPES[0].value,
            selectedTypeLabel = CONTENT_TYPES[0].text;

		editor.windowManager.open({
            width: 300,
            height: 60,
			title: 'Выбор типа',
			body: [{ type: 'listbox',
                     name: 'linkType',
                     label: 'Тип',
                     multiple: false,
                     values: CONTENT_TYPES,
                     onselect: function(v){
                        selectedType = v.control.value();
                        selectedTypeLabel = v.control.text();
                     }}],
			onsubmit: function(){
                var snippet = {};

                window.modalEditor = { //what is this???
                    editor: editor,
                    callback: function(editedData){
                        var snippetValue,
                            snippetTwig,
                            snippetHtml;

                        snippet.entityId = editedData.value;
                        snippet.label = editedData.label;
                        snippet.type = selectedType;
                        snippet.viewId = null;

                        editor.windowManager.close();
                        window.modalEditor = null;

                        snippetValue = 'Type: ' + snippet.type + ', ID: ' + snippet.entityId + ', Label: ' + snippet.label;
                        snippetTwig = '{% render url(\'damedia_foreign_entity\', { \'entity\': \'' + snippet.type + '\', \'itemId\': ' + snippet.entityId + ' }) %}';
                        snippetHtml = '<input type="button" class="snippet" value="' + snippetValue + '" data-twig="' + snippetTwig + '" disabled="disabled" />';
                        editor.execCommand('mceInsertContent', false, snippetHtml);
                    }
                };

                editor.windowManager.open({
                    type: 'window',
                    width: 700,
                    height: 500,
                    title: 'Поиск объекта',
                    url: editor.settings.parameters.selectFormUrl + '?_sonata_admin=' + editor.settings.parameters.sonataAdmin + '#'+selectedType
                }, {
                    typeTranslation : selectedTypeLabel
                });
			}
		});
	}

	editor.addButton('snippetAdd', {
		text: 'Snippet',
		toolbar: 'snippet',
		icon: false,
		onclick: createSnippet
	});
});