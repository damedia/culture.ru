/**
 * plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*jshint unused:false */
/*global tinymce:true */

/**
 * Example plugin that adds a toolbar button and menu item.
 */
tinymce.PluginManager.add("snippet", function(editor, url) {
	var CONTENT_TYPES =	editor.settings.snippet.types || [{value:"news", text:"News"}];

	function serializeToNode(snippet){
		var sn = serializeSnippet(snippet), //remove?
		    aNode = document.createElement("img");

		renderSnippet(aNode, snippet);

		return aNode.outerHTML;
	}

	function wrapText(context, text, x, y, maxWidth, lineHeight){
        var words = text.split(" "),
            line = "",
            n;

        for (n = 0; n < words.length; n++) {
            var testLine = line + words[n] + " ",
                metrics = context.measureText(testLine),
                testWidth = metrics.width;

            if (testWidth > maxWidth && n > 0) {
                context.fillText(line, x, y);
                line = words[n] + " ";
                y += lineHeight;
            }
            else {
                line = testLine;
            }
        }

        context.fillText(line, x, y);
    }

	function renderSnippet(node, snippet){
		var canvas = document.createElement("canvas"),
		    ctx,
		    typeName = null,
		    i;

		canvas.width = 300;
		canvas.height = 200; 

		ctx = canvas.getContext("2d");
		ctx.fillStyle = "#779";
		ctx.strokeStyle = "#000";
		ctx.font = "14pt Arial";

		for(i = 0; i < CONTENT_TYPES.length && !typeName; i++) {
			if (CONTENT_TYPES[i].value == snippet.type) {
				typeName = CONTENT_TYPES[i].text; 
			}
		}

		ctx.fillText(typeName, 20, 30);
		ctx.fillText("ID:"+snippet.entityId, 20, 50);
		ctx.font = "10pt Arial";
		wrapText(ctx, snippet.label, 20, 80, 120, 15);
        
		//ctx.fillText();
		//ctx.fillText("View:"+snippet.viewId, 20, 110);

		node.dataset['snippet'] = serializeSnippet(snippet);
		node.src = canvas.toDataURL();
	}

	function serializeSnippet(snippet){
		return escape(JSON.stringify(snippet));
	}

	function unserializeSnippet(text){
		// TODO: regexp.match text against snippet pattern
		var s;

		try {
			eval("s="+text+";");
		}
		catch(e) {
			return false;
		}

		return (s);
	}

	function editSnippet(snippet, onEdited){
		if (!snippet) {
			createSnippet();
			return;
		}

		if (!snippet.type) {
			createSnippet();
			return;
		}
		
		(function(){
		/*
			var searcher  = tinymce.ui.Factory.create({ type: 'textbox', name: 'aa'});
			var panel = tinymce.ui.Factory.create({ type: 'panel', name: 'aa'});
			searcher.on('keydown', function(e) {
					console.log("keydown", searcher.value() );
					/*$.ajax('admin/news/jsonlist/'+searcher.value, {
						// asynmc: false,
						success : function(data) {
							
							
						},
						error : function(xhr, err) {
							console.log('xhr error', xhr, err);
							 
						} 
					}); * /
				});
			
			
			editForm=tinymce.ui.Factory.create({type:'container', name:"fmSelectType", 
						style:"position:relative;float:left;", 
						items:[{type:'label', text:'Entity'}, searcher,panel]});			
		*/
			window.modalEditor = {
				"editor": editor,
				callback: function(editedData){
				// Insert content when the window form is submitted
				
				// snippet.entityId=(!snippet.entityId?1:snippet.entityId+1);

				snippet.entityId = editedData.value;
				snippet.label = editedData.label;
				snippet.viewId = !!snippet.viewId ? "A_"+snippet.viewId : "aaa.twig.html";
			   	
				//console.log("edit submit", editedData, snippet);
			   	
				editor.windowManager.close();
				window.modalEditor = null;
			   	
				if (typeof onEdited == "function") {
					onEdited(snippet);
				}
			}};

			//console.log(editor.settings.snippet);

			editor.windowManager.open({
				"type": "window",
				"width": 700,
				"height": 500,
				"title": "Insert internal content box",
				"url": editor.settings.snippet.selectFormUrl+"?_sonata_admin="+editor.settings.snippet.sonataAdmin+"#"+snippet.type
				/*
				"html": '<div class="ui-widget"><form action="#"><label for="tags">Tags: </label><input id="tags" /></div><script> $("#go").on("click", function(e){console.log(window.parent, $("#tags")[0].value);});'+
 '$(function() {    var availableTags = [      "ActionScript",      "AppleScript",      "Asp",      "BASIC",      "C",      "C++",      "Clojure",      "COBOL",      "ColdFusion",      "Erlang",      "Fortran", '+
      '"Groovy",      "Haskell",      "Java",      "JavaScript",      "Lisp",      "Perl",      "PHP",      "Python",      "Ruby",      "Scala",      "Scheme"    ];'+
    '$( "#tags" ).autocomplete({      source: availableTags    });  }); </script>'
				"body": [ editForm ],
				onsubmit: function(e){
					// Insert content when the window form is submitted
					
					snippet.entityId = !snippet.entityId ? 1 : (snippet.entityId + 1);
					snippet.viewId = !!snippet.viewId ? "A_"+snippet.viewId : "aaa.twig.html";

					//console.log("edit submit", e.data, snippet);

					if (typeof onEdited == "function") {
						onEdited(snippet);
					}
				}
				*/
			}); 
		}());
	}

	function createSnippet(){
		// Open window
		editor.windowManager.open({
			"title": "Insert internal content box",
			"body": [ { "type": "listbox", "name": "linkType", "label": "Тип",  "multiple": false, "values": CONTENT_TYPES } ],
			onsubmit: function(e){
				// Insert content when the window form is submitted

				//console.log("submit", e.data);

				editSnippet({ "type": e.data.linkType || CONTENT_TYPES[0].value, "label": "", "entityId": null, "viewId": null }, function(snp){
					editor.insertContent(serializeToNode(snp));
				});
			}
		});
	}

	editor.addButton("snippetAdd", {
		"text": "+ link",
		"toolbar": "snippet",
		"icon": false,
		onclick: createSnippet
	});

	editor.addButton("snippetEdit", {
		"text": "edit link",
		"toolbar": "snippet",
		"icon": false,
		onclick: function(){
			//var text = editor.selection.getContent({ "format": "text" }),
			//    snippet = unserializeSnippet(text);

			var node = editor.selection.getNode(),
			    snippet = unescape(node.dataset["snippet"]);

			if (snippet) {
				snippet = unserializeSnippet(snippet);
			}
			
			//console.log(snippet);
				
			if (snippet) {
				editSnippet(snippet, function(snp){
					node.dataset["snippet"] = serializeSnippet(snp);
					renderSnippet(node, snp);
				});
			}
		}
	});

	// Adds a menu item to the tools menu
	editor.addMenuItem("example", {
		"text": "Example plugin",
		"context": "tools",
		onclick: function(){
			// Open window with a specific url
			editor.windowManager.open({
				"title": "TinyMCE site",
				"url": "http://www.tinymce.com",
				"width": 800,
				"height": 600,
				"buttons": [{
					"text": "Close",
					onclick: "close"
				}]
			});
		}
	});
});