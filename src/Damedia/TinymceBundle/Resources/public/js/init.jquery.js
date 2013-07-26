/**
 * Initialize standard build of the TinyMCE
 *
 * @param options
 */
function initTinyMCE(options) {
    (function ($, undefined) {
        $(function () {
            var $tinymceTargets;

            if(options.textarea_class){
                $tinymceTargets = $('textarea' + options.textarea_class);
            } else {
                $tinymceTargets = $('textarea');
            }

            $tinymceTargets.each(function () {
                var $textarea = $(this),
                    theme = $textarea.data('theme') || 'simple';

                // Get selected theme options
                var themeOptions = (typeof options.theme[theme] != 'undefined')
                    ? options.theme[theme]
                    : options.theme['simple'];

                themeOptions.script_url = options.jquery_script_url;
                themeOptions.sonataAdmin = options.sonataAdmin || "";
/*
                // workaround for an incompatibility with html5-validation (see: http://git.io/CMKJTw)
                if ($textarea.is('[required]')) {
                    themeOptions.oninit = function (editor) {
                        editor.onChange.add(function (ed) {
                            ed.save();
                        });
                    };
                }
                */
                
                themeOptions.setup = function(ed) {
                    // Add custom buttons to current editor
                    $.each(options.tinymce_buttons || {}, function(id, opts) {
                        opts = $.extend({}, opts, {
                            onclick: function() {
                                var callback = window['tinymce_button_' + id];
                                if (typeof callback == 'function') {
                                    callback(ed);
                                } else {
                                    alert('You have to create callback function: "tinymce_button_' + id + '"');
                                }
                            }
                        });
                        ed.addButton(id, opts);
                    });

                    // Load external plugins
                    $.each(options.external_plugins || {}, function(id, opts) {
                        var url = opts.url || null;
                        if (url) {
                            tinymce.PluginManager.load(id, url);
                        }
                    });
                 
                };
                if (typeof themeOptions.file_browser_callback=="string") {
                	themeOptions.file_browser_callback = eval("window."+themeOptions.file_browser_callback);
                }
                $textarea.tinymce(themeOptions);
            });
        });
    }(jQuery));
}