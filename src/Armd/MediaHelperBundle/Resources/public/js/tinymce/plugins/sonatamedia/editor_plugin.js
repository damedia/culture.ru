(function() {
	
    window.SonataMedia = {
        mediaListUrl:   Routing.getBaseUrl() + "/admin/sonata/media/media/list?context=default",
        mediaCreateUrl: Routing.getBaseUrl() + "/admin/sonata/media/media/create",
        mediaInfoUrl:   Routing.getBaseUrl() + "/admin/armd/media/path/",
        
        browse: function(fieldName, url, type, win) {
        	
        	
            var dialog = tinyMCE.activeEditor.windowManager.open({
                    title:  "Sonata media",
                    inline: true,
                    width:  1000,
                    height: $(window).height() - 200
                });
            
            console.log(SonataMedia.mediaListUrl + "&code=sonata.media.admin.media&uniqid=" + SonataMedia.uniqid());
            
            $.get(SonataMedia.mediaListUrl + "&uniqid=" + SonataMedia.uniqid())
              .done(function(html) {
            	  
                SonataMedia.setDialogContent(dialog, html);

                $("#" + dialog.id + "_content")
                    .find("a")
                        .live("click", function(event) {
                            event.preventDefault();
                            event.stopPropagation();

                            var element = $(this).parents("#" + dialog.id + " .sonata-ba-list-field");

                            if (element.length == 0 && $(this).attr("href")) {
                                $.get($(this).attr("href")).done(function(html) {
                                   SonataMedia.setDialogContent(dialog, html);
                                });

                                return;
                            }

                            var mediaId = $(this).parents("td.sonata-ba-list-field[objectid]").attr("objectid");
                            if (mediaId) {
                                $.get(SonataMedia.mediaInfoUrl + mediaId).done(function(objectData) {
                                    SonataMedia.selectMedia(win, dialog, fieldName, objectData);
                                });
                            }
                        });

                $("#" + dialog.id + "_content form").live("submit", function(event) {
                    event.preventDefault();

                    var form = $(this);

                    $(form).ajaxSubmit({
                        type: form.attr("method"),
                        url: form.attr("action"),
                        dataType: "html",
                        data: {_xml_http_request: true},
                        success: function(data) {
                            if (typeof data == "string") {
                                SonataMedia.setDialogContent(dialog, data);
                                return;
                            };

                            if (data.result == "ok") {
                                $.ajax({url: SonataMedia.mediaInfoUrl + data.objectId}).done(function(objectData) {
                                    SonataMedia.selectMedia(win, dialog, fieldName, objectData);
                                });

                                return;
                            }
                        }
                    });
                });
            });
        },
        
        setDialogContent: function(dialog, content) {
            content = "" +
                "<div class=\"sonata-ba-model\" style=\"padding: 0 20px; overflow-x: hidden; overflow-y: scroll; width: " + ($("#" + dialog.id + "_content").width() - 40) + "px; height: " + $("#" + dialog.id + "_content").height() + "px;\">" +
                    "<p style=\"margin-top: -3px; text-align: right;\">" +
                        "<a class=\"btn\" href=\"" + SonataMedia.mediaCreateUrl + "\">" +
                            "<i class=\"icon-plus\"></i>" +
                            "Добавить новый" +
                        "</a> " +
                        "<a class=\"btn\" href=\"" + SonataMedia.mediaListUrl + "\">" +
                            "<i class=\"icon-list\"></i>" +
                            "Вернуться к списку" +
                        "</a>" +
                    "</p>" +
                    content +
                "</div>";
            $("#" + dialog.id + "_content")
                .html(content)
                .find(".sonata-ba-model div, .sonata-ba-model a")
                    .css({
                        position:   "static",
                        top:        "auto",
                        left:       "auto",
                        width:      "auto",
                        height:     "auto",
                        fontWeight: "normal"
                    })
                .filter("div")
                    .css("width", "100%");
        },
        selectMedia: function(win, dialog, fieldName, objectData) {
        	var inp=$(win.document).contents().find("input[name='" + fieldName + "']");
        	if (!inp.length)
        		inp=$(win.document).contents().find("#" + fieldName);
        	inp.val(objectData);

            if (win.ImageDialog) {
                if (win.ImageDialog.getImageData) {
                    win.ImageDialog.getImageData();
                }

                if (win.ImageDialog.showPreviewImage) {
                    win.ImageDialog.showPreviewImage(objectData);
                }
            }

            tinyMCE.activeEditor.windowManager.close(dialog.id);
        },
        uniqid: function(prefix, more_entropy) {
            // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +    revised by: Kankrelune (http://www.webfaktory.info/)
            // %        note 1: Uses an internal counter (in php_js global) to avoid collision
            // *     example 1: uniqid();
            // *     returns 1: 'a30285b160c14'
            // *     example 2: uniqid('foo');
            // *     returns 2: 'fooa30285b1cd361'
            // *     example 3: uniqid('bar', true);
            // *     returns 3: 'bara20285b23dfd1.31879087'
            if (typeof prefix == 'undefined') {
                prefix = "";
            }

            var retId;
            var formatSeed = function (seed, reqWidth) {
                seed = parseInt(seed, 10).toString(16); // to hex str
                if (reqWidth < seed.length) { // so long we split
                    return seed.slice(seed.length - reqWidth);
                }
                if (reqWidth > seed.length) { // so short we pad
                    return Array(1 + (reqWidth - seed.length)).join('0') + seed;
                }
                return seed;
            };

            // BEGIN REDUNDANT
            if (!this.php_js) {
                this.php_js = {};
            }
            // END REDUNDANT
            if (!this.php_js.uniqidSeed) { // init seed with big random int
                this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
            }
            this.php_js.uniqidSeed++;

            retId = prefix; // start with prefix, add current milliseconds hex string
            retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
            retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
            if (more_entropy) {
                // for more entropy we add a float lower to 10
                retId += (Math.random() * 10).toFixed(8).toString();
            }

            return retId;
        }
    };
})();
