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
		return ('<canvas class="snippet '+snippet.type+'" data-snippet="'+sn+'"></canvas>');
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
	function renderSnippet(canvas, snippet) {
		var ctx = canvas.getContext("2d");
	//	canvas.width = canvas.width;
		ctx.fillStyle = "#F00";
		ctx.strokeStyle = "#000";
		ctx.font = "30pt Arial";
		ctx.moveTo(0,0);
		ctx.lineTo(200,100);
		ctx.stroke();

		ctx.fillText(CONTENT_TYPES[snippet.type], 20, 50);
		ctx.fillText("ID:"+snippet.entityId, 20, 80);
		ctx.fillText("View:"+snippet.viewId, 20, 110);
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
		editForm=tinymce.ui.Factory.create({type:'container', name:"fmSelectType", 
					style:"position:relative;float:left;", 
					items:[{type:'label', text:'Entity'}, { type: 'textbox', name: 'aa'}]});
		
		editor.windowManager.open({
				title: 'Insert internal content box',
				body: [
					editForm
				],
			/*	buttons:[{text: 'Cancel', onclick: function(e) {
						console.log(this);
						this._parent._parent.close();
					//	this.form.close();
					}}], */
				onsubmit: function(e) {
					// Insert content when the window form is submitted
					
					snippet.entityId=(!snippet.entityId?1:snippet.entityId+1);
					snippet.viewId=(!!snippet.viewId?'A_'+snippet.viewId:"aaa.twig.html");
					console.log("edit submit", e.data, snippet);
					if (typeof onEdited == 'function')
						onEdited(snippet);
				}
			});
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
						var node=editor.selection.getNode();
						if (node.tagName!='canvas')
								node=node.firstChild;
						if (node.tagName=='canvas')
						 renderSnippet(node, snp);
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
						// node.outerHTML=
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