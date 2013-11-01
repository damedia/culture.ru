/**
 * To use this reference javascript, you must also have jQuery installed. If
 * you want to embed comments cross-domain, then easyXDM CORS is also required.
 *
 * @todo: expand this explanation (also in the docs)
 *
 * Then a comment thread can be embedded on any page:
 *
 * <div id="fos_comment_thread">#comments</div>
 * <script type="text/javascript">
 *     // Set the thread_id if you want comments to be loaded via ajax (url to thread comments api)
 *     var fos_comment_thread_id = 'a_unique_identifier_for_the_thread';
 *     var fos_comment_thread_api_base_url = 'http://example.org/api/threads';
 *
 *     // Optionally set the cors url if you want cross-domain AJAX (also needs easyXDM)
 *     var fos_comment_remote_cors_url = 'http://example.org/cors/index.html';
 *
 *     // Optionally set a custom callback function to update the comment count elements
 *     var window.fos_comment_thread_comment_count_callback = function(elem, threadObject){}
 *
 *     // Optionally set a different element than div#fos_comment_thread as container
 *     var fos_comment_thread_container = $('#other_element');
 *
 * (function() {
 *     var fos_comment_script = document.createElement('script');
 *     fos_comment_script.async = true;
 *     fos_comment_script.src = 'http://example.org/path/to/this/file.js';
 *     fos_comment_script.type = 'text/javascript';
 *
 *     (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(fos_comment_script);
 * })();
 * </script>
 */

(function(window, $, easyXDM){
    'use strict';

    var FOS_COMMENT = {
        /**
         * Shorcut post method. Wrap the error callback to match return data between jQuery and easyXDM
         *
         * @param string url The url of the page to post.
         * @param object data The data to be posted.
         * @param function success Optional callback function to use in case of succes.
         * @param function error Optional callback function to use in case of error.
         */
        post: function(url, data, success, error){
            var wrappedErrorCallback = function(response){
                if('undefined' !== typeof error) {
                    error(response.responseText, response.status);
                }
            };

            $.post(url, data, success).error(wrappedErrorCallback);
        },

        /**
         * Shorcut get method. Wrap the error callback to match return data between jQuery and easyXDM
         *
         * @param string url The url of the page to get.
         * @param object data The query data.
         * @param function success Optional callback function to use in case of succes.
         * @param function error Optional callback function to use in case of error.
         */
        get: function(url, data, success, error) {
            var wrappedErrorCallback = function(response){
                if('undefined' !== typeof error) {
                    error(response.responseText, response.status);
                }
            };

            $.get(url, data, success).error(wrappedErrorCallback);
        },

        /**
         * Gets the comments of a thread and places them in the thread holder.
         *
         * @param string identifier Unique identifier url for the thread comments.
         * @param string url Optional url for the thread. Defaults to current location.
         */
        getThreadComments: function(identifier, permalink){
            if('undefined' == typeof permalink) {
                permalink = window.location.href;
            }

            FOS_COMMENT.get(FOS_COMMENT.base_url + '/' + encodeURIComponent(identifier) + '/comments', { permalink: encodeURIComponent(permalink) }, function(data){
                FOS_COMMENT.thread_container.html(data);
                FOS_COMMENT.thread_container.attr('data-thread', identifier);
                FOS_COMMENT.thread_container.trigger('comments_loaded');

                $('.palette-text', FOS_COMMENT.thread_container).addClass(fos_comment_text_color_class);
                $('.palette-button', FOS_COMMENT.thread_container).addClass(fos_comment_new_comment_post_button_class);

                if (/comment\d+/.test(location.hash)) {
                    var hashtag_comment_div = $('div'+location.hash, FOS_COMMENT.thread_container);

                    $('html').scrollTop(hashtag_comment_div.offset().top);
                }
            });
        },

        loadingMessage: function(){
            var loadingMessageDiv = $('#comment_loading_message').clone();

            FOS_COMMENT.thread_container.html('').append(loadingMessageDiv);
            loadingMessageDiv.show();
        },

        /**
         * Initialize the event listeners.
         */
        initializeListeners: function(){
            FOS_COMMENT.thread_container.on('click', 'button.comment-post-button', function(e){
                var button = $(this),
                    form = button.closest('form.fos_comment_comment_new_form'),
                    textarea = form.find('textarea');

                if (!textarea.size()) {
                    return false;
                }

                if (textarea.val()) {
                    form.submit();

                    return true;
                }
                else {
                    return false;
                }
            });

            FOS_COMMENT.thread_container.on('submit', 'form.fos_comment_comment_new_form', function(e){
                var that = $(this);

                FOS_COMMENT.post(this.action, FOS_COMMENT.serializeObject(this),
                    function(data){ //success
                        FOS_COMMENT.appendComment(data, that);
                    },
                    function(data){ //error
                        var parent = that.parent();

                        parent.after(data);
                        parent.remove();
                    }
                );

                e.preventDefault();
            });

            FOS_COMMENT.thread_container.on('submit', 'form.fos_comment_comment_edit_form', function(e){
                var that = $(this);

                FOS_COMMENT.post(this.action, FOS_COMMENT.serializeObject(this),
                    function(data){ //success
                        FOS_COMMENT.editComment(data);
                    },
                    function(data){ //error
                        var parent = that.parent();

                        parent.after(data);
                        parent.remove();
                    }
                );

                e.preventDefault();
            });


            /*=======================
            ==== click handlers ===*/
            FOS_COMMENT.thread_container.on('click', '#show_add_new_comment', function(e){
                var button = $('#add_new_comment');

                if (button.is(':visible')) {
                    button.hide();
                }
                else {
                    button.show();
                }
            });

            FOS_COMMENT.thread_container.on('click', '.fos_comment_comment_reply_show_form', function(e){
                var form_data = $(this).data(),
                    that = $('#comment'+form_data.parentId).find('.fos_comment_comment_reply');

                e.preventDefault();

                if (that.children('.fos_comment_comment_form_holder').length > 0) {
                    return false;
                }

                FOS_COMMENT.get(form_data.url, { parentId: form_data.parentId }, function(data){
                    $(that).addClass('fos_comment_replying').html(data);
                    $(that).find('button.comment-post-button').addClass(fos_comment_new_comment_post_button_class);
                });
            });

            FOS_COMMENT.thread_container.on('click', '.fos_comment_comment_reply_cancel', function(e){
                var form_holder = $(this).parent().parent().parent();

                form_holder.parent().removeClass('fos_comment_replying');
                form_holder.remove();
            });

            FOS_COMMENT.thread_container.on('click', '.fos_comment_comment_edit_show_form', function(e){
                var form_data = $(this).data(),
                    that = this;

                FOS_COMMENT.get(form_data.url, { }, function(data){
                    var commentBody = $(that).parent().prev();

                    commentBody.data('original', commentBody.html()); //save the old comment for the cancel function
                    commentBody.html(data); //show the edit form
                });
            });

            FOS_COMMENT.thread_container.on('click', '.fos_comment_comment_edit_cancel', function(e){
                FOS_COMMENT.cancelEditComment($(this).parents('.fos_comment_comment_body'));
            });

            FOS_COMMENT.thread_container.on('click', '.fos_comment_comment_vote', function(e){
                var form_data = $(this).data();

                FOS_COMMENT.get(form_data.url, { }, function(data){
                    var form = $(data).children('form')[0],
                        form_data = $(form).data();

                    FOS_COMMENT.post(form.action, FOS_COMMENT.serializeObject(form), function(data){
                        $('#' + form_data.scoreHolder).html(data);
                    });
                });
            });

            FOS_COMMENT.thread_container.on('click', '.fos_comment_comment_remove', function(e){
                var form_data = $(this).data();

                FOS_COMMENT.get(form_data.url, { }, function(data){
                    var form = $(data).children('form')[0];

                    FOS_COMMENT.post(form.action, FOS_COMMENT.serializeObject(form), function(data){
                        var commentHtml = $(data),
                            originalComment = $('#' + commentHtml.attr('id'));

                        originalComment.replaceWith(commentHtml);
                        commentHtml.find('.palette-text').addClass(fos_comment_text_color_class);
                    });
                });
            });

            FOS_COMMENT.thread_container.on('click', '.fos_comment_thread_commentable_action', function(e){
                var form_data = $(this).data();

                FOS_COMMENT.loadingMessage();

                FOS_COMMENT.get(form_data.url, { }, function(data){
                    var form = $(data).children('form')[0];

                    FOS_COMMENT.post(form.action, FOS_COMMENT.serializeObject(form), function(data){
                        var form = $(data).children('form')[0],
                            threadId = $(form).data().fosCommentThreadId;

                        FOS_COMMENT.getThreadComments(threadId); //reload the intire thread
                    });
                });
            });
        },

        appendComment: function(commentHtml, form){
            var form_data = form.data(),
                newComment = $('<li>' + commentHtml + '</li>');

            $('.palette-text', newComment).addClass(fos_comment_text_color_class);

            if (form_data.parent != '') {
                var form_parent = form.parent(),
                    reply_button_holder = form_parent.parent();

                reply_button_holder.removeClass('fos_comment_replying');
                $('#comments_list_' + form_data.parent).prepend(newComment);
                form_parent.remove();
            }
            else {
                var post_comments_holder = $('#post-comments'),
                    comments_list_holder = $('#comments_list_0');

                if ($('#fos_comment_thread').data('thread').indexOf('blog') > -1) {
                    post_comments_holder.find('.validation-info').show();
                    post_comments_holder.find('.comment-item:first').before(commentHtml);
                }
                else {
                    comments_list_holder.prepend(newComment);
                    comments_list_holder.parent().addClass('comments-block');
                    $('#comments_header').addClass('comments-header');
                    $('#add_new_comment').hide();
                }

                //"reset" the form
                form = $(form[0]);
                form.find('textarea').val('');
                form.children('.fos_comment_form_errors').remove();
            }

            FOS_COMMENT.thread_container.trigger('comment_appended');
        },
        editComment: function(commentHtml){
            var commentHtml = $(commentHtml),
                originalCommentBody = $('#' + commentHtml.attr('id')).children('.comment-list_text').children('.fos_comment_comment_body');

            originalCommentBody.html(commentHtml.children('.comment-list_text').children('.fos_comment_comment_body').html());
        },
        cancelEditComment: function(commentBody) {
            commentBody.html(commentBody.data('original'));
        },

        /**
         * easyXdm doesn't seem to pick up 'normal' serialized forms yet in the
         * data property, so use this for now.
         * http://stackoverflow.com/questions/1184624/serialize-form-to-json-with-jquery#1186309
         */
        serializeObject: function(obj)
        {
            var o = {};
            var a = $(obj).serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        },

        loadCommentCounts: function()
        {
            var threadIds = [];
            var commentCountElements = $('span.fos-comment-count');

            commentCountElements.each(function(i, elem){
                var threadId = $(elem).data('fosCommentThreadId');
                if(threadId) {
                    threadIds.push(threadId);
                }
            });

            FOS_COMMENT.get(
                FOS_COMMENT.base_url + '.json',
                {ids: threadIds},
                function(data) {
                    // easyXdm doesn't always serialize
                    if (typeof data != "object") {
                        data = jQuery.parseJSON(data);
                    }

                    var threadData = {};

                    for (var i in data.threads) {
                        threadData[data.threads[i].id] = data.threads[i];
                    }

                    $.each(commentCountElements, function(){
                        var threadId = $(this).data('fosCommentThreadId');
                        if(threadId) {
                            FOS_COMMENT.setCommentCount(this, threadData[threadId]);
                        }
                    });
                }
            );

        },

        setCommentCount: function(elem, threadObject) {
            if (threadObject == undefined) {
                elem.innerHTML = '0';

                return;
            }

            elem.innerHTML = threadObject.num_comments;
        }
    };

    // Check if a thread container was configured. If not, use default.
    FOS_COMMENT.thread_container = window.fos_comment_thread_container || $('#fos_comment_thread');

    // AJAX via easyXDM if this is configured
    if (typeof window.fos_comment_remote_cors_url != "undefined") {
        /**
         * easyXDM instance to use
         */
        FOS_COMMENT.easyXDM = easyXDM.noConflict('FOS_COMMENT');

        /**
         * Shorcut request method. Wrap the callbacks to match the callback parameters of jQuery
         *
         * @param string method The request method to use.
         * @param string url The url of the page to request.
         * @param object data The data parameters.
         * @param function success Optional callback function to use in case of succes.
         * @param function error Optional callback function to use in case of error.
         */
        FOS_COMMENT.request = function(method, url, data, success, error){
            var wrappedSuccessCallback = function(response){
                    if ('undefined' !== typeof success) {
                        success(response.data, response.status);
                    }
                },
                wrappedErrorCallback = function(response){
                    if('undefined' !== typeof error) {
                        error(response.data.data, response.data.status);
                    }
                };

            // todo: is there a better way to do this?
            FOS_COMMENT.xhr.request({
                url: url,
                method: method,
                data: data
            }, wrappedSuccessCallback, wrappedErrorCallback);
        };

        FOS_COMMENT.post = function(url, data, success, error){
            this.request('POST', url, data, success, error);
        };

        FOS_COMMENT.get = function(url, data, success, error){
            // make data serialization equals to that of jquery
            var params = jQuery.param(data);

            url += '' != params ? '?' + params : '';

            this.request('GET', url, undefined, success, error);
        };

        /* Initialize xhr object to do cross-domain requests. */
        FOS_COMMENT.xhr = new FOS_COMMENT.easyXDM.Rpc({
            remote: window.fos_comment_remote_cors_url
        }, {
            remote: {
                request: {} // request is exposed by /cors/
            }
        });
    }

    // set the appropriate base url
    FOS_COMMENT.base_url = window.fos_comment_thread_api_base_url;

    // Load the comment if there is a thread id defined.
    if (typeof window.fos_comment_thread_id != "undefined") {
        // get the thread comments and init listeners
        FOS_COMMENT.getThreadComments(window.fos_comment_thread_id);
    }

    if (typeof window.fos_comment_thread_comment_count_callback != "undefined") {
        FOS_COMMENT.setCommentCount = window.fos_comment_thread_comment_count_callback;
    }

    if ($('span.fos-comment-count').length > 0) {
        FOS_COMMENT.loadCommentCounts();
    }

    FOS_COMMENT.initializeListeners();

    window.fos = window.fos || {};
    window.fos.Comment = FOS_COMMENT;
})(window, window.jQuery, window.easyXDM);