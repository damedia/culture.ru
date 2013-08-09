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
tinymce.PluginManager.add('example', function(editor, url) {
	// Add a button that opens a window
	var CONTENT_TYPES=[
	     {value:"news", text:"Новость"},
	     {value:"article", text:"Статья"},
		 {value:"realMuseum", text:"Музей"},
		 {value:"museum", text:"Вирутальный тур"},
		 {value:"artObject", text:"Артефакт"},
		 {value:"lecture", text:"Лекция"},
	];
	function serializeToNode(snippet) {
		var sn=serializeSnippet(snippet);
/*		var aNode=document.createElement('div');
		aNode.dataset['snippet']=sn;
		return aNode;
*/		
		/*
		var canvas = document.createElement('canvas');
		canvas.width = 300;
		canvas.height = 200; 
		var ctx = canvas.getContext("2d");
		
	//	canvas.width = canvas.width;
		ctx.fillStyle = "#F00";
		ctx.strokeStyle = "#000";
		ctx.font = "30pt Arial";
		ctx.fillText(CONTENT_TYPES[snippet.type], 20, 50);
		ctx.fillText("ID:"+snippet.entityId, 20, 80);
		ctx.fillText("View:"+snippet.viewId, 20, 110); */
		
		var aNode=document.createElement('img');
		renderSnippet(aNode, snippet);
		/* aNode.dataset['snippet']=sn;
		aNode.src=canvas.toDataURL(); */
		return aNode.outerHTML; 
		// return ('<div class="snippet '+snippet.type+'" data-snippet="'+sn+'"></div>');
	}
	function renderSnippet(node, snippet) {
		var canvas = document.createElement('canvas');
		canvas.width = 300;
		canvas.height = 200; 
		var ctx = canvas.getContext("2d");
		
	//	canvas.width = canvas.width;
		ctx.fillStyle = "#779";
		ctx.strokeStyle = "#000";
		ctx.font = "14pt Arial";
		var typeName=null;
			for(var i=0;i<CONTENT_TYPES.length && !typeName; i++) {
				if (CONTENT_TYPES[i].value==snippet.type)
					typeName=CONTENT_TYPES[i].text; 
			};
		ctx.fillText(typeName, 20, 50);
		ctx.fillText("ID:"+snippet.entityId, 20, 80);
		ctx.fillText("View:"+snippet.viewId, 20, 110);
		node.dataset['snippet']=serializeSnippet(snippet);
		node.src=canvas.toDataURL();
	}
	
	function serializeSnippet(snippet) {
		return escape(JSON.stringify(snippet));
	}
	function unserializeSnippet(text) {
		// TODO: regexp.match text against snippet pattern
		var s;
		try {
			eval('s='+text+';');
		} catch(e) {
			return false;
		}
		return (s);
	}
	
	function editSnippet(snippet, onEdited) {
		if (!snippet) {
			createSnippet();
			return;
		};
		if (!snippet.type) {
			createSnippet();
			return;
		};
		
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
			window.modalEditor={editor:editor, callback:function(editedData) {
						// Insert content when the window form is submitted
						
						// snippet.entityId=(!snippet.entityId?1:snippet.entityId+1);
						snippet.entityId=editedData.value;
						snippet.viewId=(!!snippet.viewId?'A_'+snippet.viewId:"aaa.twig.html");
						console.log("edit submit", editedData, snippet);
						editor.windowManager.close();
						window.modalEditor = null;
						if (typeof onEdited == 'function')
							onEdited(snippet);
					}};

			editor.windowManager.open({
					type:'window',
					title: 'Insert internal content box',
					url: "selectForm.html",
/*					html:'<div class="ui-widget"><form action="#"><label for="tags">Tags: </label><input id="tags" /></div><script> $("#go").on("click", function(e){console.log(window.parent, $("#tags")[0].value);});'+
 '$(function() {    var availableTags = [      "ActionScript",      "AppleScript",      "Asp",      "BASIC",      "C",      "C++",      "Clojure",      "COBOL",      "ColdFusion",      "Erlang",      "Fortran", '+
      '"Groovy",      "Haskell",      "Java",      "JavaScript",      "Lisp",      "Perl",      "PHP",      "Python",      "Ruby",      "Scala",      "Scheme"    ];'+
    '$( "#tags" ).autocomplete({      source: availableTags    });  }); </script>'
					body: [
						editForm
					],
					onsubmit: function(e) {
						// Insert content when the window form is submitted
						
						snippet.entityId=(!snippet.entityId?1:snippet.entityId+1);
						snippet.viewId=(!!snippet.viewId?'A_'+snippet.viewId:"aaa.twig.html");
						console.log("edit submit", e.data, snippet);
						if (typeof onEdited == 'function')
							onEdited(snippet);
					}
			*/
				}); 
		})();
		
		
		
	}
	function createSnippet() {
			// Open window
			editor.windowManager.open({
				title: 'Insert internal content box',
				body: [
						{ type: 'listbox', name: 'linkType', label: 'Тип',  multiple:false,  values:CONTENT_TYPES}, 
				],
				onsubmit: function(e) {
					// Insert content when the window form is submitted
					console.log("submit", e.data);
					editSnippet({type:e.data.linkType, entityId:null, viewId:null}, function(snp) {
						editor.insertContent(serializeToNode(snp));
					});
				}
			});
		}
	editor.addButton('example1', {
		text: '+ link',
		toolbar: 'example',
		icon: false,
		onclick: createSnippet
	});
	editor.addButton('example', {
		text: 'edit link',
		toolbar: 'example',
		icon: false,
		onclick: function() {
				//var text=editor.selection.getContent({format: 'text'})
				//var snippet=unserializeSnippet(text);
				var node=editor.selection.getNode();
				var snippet=unescape(node.dataset["snippet"]);
				if (snippet) {
					snippet=unserializeSnippet(snippet);
				}
				console.log(snippet);
				
				if (snippet) {
					editSnippet(snippet, function(snp) {
						node.dataset["snippet"]=serializeSnippet(snp);
						renderSnippet(node, snp);
					});
				}
		}
	});
	// Adds a menu item to the tools menu
	editor.addMenuItem('example', {
		text: 'Example plugin',
		context: 'tools',
		onclick: function() {
			// Open window with a specific url
			editor.windowManager.open({
				title: 'TinyMCE site',
				url: 'http://www.tinymce.com',
				width: 800,
				height: 600,
				buttons: [{
					text: 'Close',
					onclick: 'close'
				}]
			});
		}
	});
});