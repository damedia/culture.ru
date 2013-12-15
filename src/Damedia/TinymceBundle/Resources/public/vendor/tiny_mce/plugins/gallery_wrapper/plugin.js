(function() {
    tinymce.create('tinymce.plugins.GalleryWrapperPlugin', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
            ed.addCommand('galleryConstructor', function(){
                var selectedNode = $(ed.selection.getNode());

                ed.windowManager.open({
                    type: "window",
                    width: 700,
                    height: 500,
                    title: "Gallery constructor",
                    url: ed.settings.parameters.galleryFormUrl+"?_sonata_admin="+ed.settings.parameters.sonataAdmin
                });

                //check selection
                if (selectedNode.prop("tagName") === 'UL' && selectedNode.hasClass("slides")) {
                    alert('Lets try to wrap it up!'); //check if not already
                }

                console.log(selectedNode);

                /*
                 var se = ed.selection.getContent();

                 if (se.trim() == "") {
                 alert("Nothing Selected.");
                 return;
                 }

                 var s1 = '<div class="myClass" >';
                 s1 += se + '</div>';

                 ed.selection.setContent(s1); - See more at: http://test.efreedom.com/Question/1-11808334/Wrapper-TinyMce-Custom-Plugin#sthash.IlhwdERm.dpuf
                */

                //console.log(ed.selection);
                //console.log(ed.selection.getNode().nodeName);

                /*
                var node = ed.selection.getNode(),
                    snippet = unescape(node.dataset["snippet"]);

                if (snippet) {
                    snippet = unserializeSnippet(snippet);
                }

                if (snippet) {
                    editSnippet(snippet, function(snp){
                        node.dataset["snippet"] = serializeSnippet(snp);
                        renderSnippet(node, snp);
                    });
                }
                */
            });

            // Register example button
            ed.addButton('galleryWrapper', {
                title : 'Apply image gallery',
                cmd : 'galleryConstructor',
                text: "G"
            });

            /*
            // Add a node change handler, selects the button in the UI when a image is selected
            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('example', n.nodeName == 'IMG');
            });
            */
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'Example plugin',
                author : 'Some author',
                authorurl : 'http://tinymce.moxiecode.com',
                infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('gallery_wrapper', tinymce.plugins.GalleryWrapperPlugin);
})();