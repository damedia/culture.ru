$(document).ready(function () {
    "use strict";


    /**
     * AJAX request entity.
     */
    var ajaxRequest = null;
    /**
     * Common handlers.
     */
    $.extend({
        // Common AJAX handler.
        commonAJAX: {
            processData: function (action, data, successCallback, isJSON, options) {
                var in_d = (isJSON) ? $.parseJSON(data) : data;

                var defaultOptions = {
                    url: action,
                    data: (isJSON) ? $.parseJSON(data) : data,
                    dataType: 'json',
                    cache: false,
                    global: false,
                    type: 'POST',
                    success: function (data, status, xhr) {

                        if (typeof successCallback === 'function') {
                            successCallback.call(this, data);
                        }
                    },
                    // HTTP status codes handler
                    statusCode: {
                        401: function () {
                            //document.location.href = '/login';
                        },
                        404: function () {
                            //window.location.reload();
                        },
                        500: function () {
                            //window.location.reload();
                        }
                    }

                    //error: function(jqXHR, textStatus, errorThrown) {
                    //    alert(textStatus);
                    //}
                };
                $.extend(defaultOptions, options);
                ajaxRequest = $.ajax(defaultOptions);
            },

            getRequest: function () {
                return ajaxRequest;
            },

            setRequest: function (val) {
                ajaxRequest = val;
            },

            processForm: function (caller, successCallback, options) {
                var in_d = $(caller).serialize();

                var defaultOptions = {
                    url: $(caller).attr('action'),
                    data: $(caller).serialize(),
                    dataType: 'json',
                    cache: false,
                    type: 'POST',
                    success: function (data, status, xhr) {
                        data.input_data = in_d;

                        if (typeof successCallback === 'function') {
                            successCallback.call(this, data);
                        }
                    },
                    // HTTP status codes handler
                    statusCode: {
                        401: function () {
                            // document.location.href = '/login';
                        },
                        404: function () {
                            // window.location.reload();
                        },

                        500: function () {
                            //   window.location.reload();
                        }
                    },

                    error: function (data) {
                    }
                };
                $.extend(defaultOptions, options);
                ajaxRequest = $.ajax(defaultOptions);
            }
        },
        setCookie: function (name, value, expires, path, domain, secure) {
            var today = new Date();
            today.setTime(today.getTime());

            if (expires) {
                expires = expires * 1000 * 60 * 60 * 24;
            }

            var expiresDate = new Date(today.getTime() + (expires));

            if (!path) {
                path = '/';
            }

            document.cookie = name + "=" + value +
                ((expires) ? ";expires=" + expiresDate.toGMTString() : "") +
                ((path) ? ";path=" + path : "") +
                ((domain) ? ";domain=" + domain : "") +
                ((secure) ? ";secure" : "");
        },

        unsetCookie: function (name) {
            document.cookie = name + "=" + '' + ";expires=-1";
        },

        getCookie: function (name) {
            var allCookies = document.cookie.split(';');
            var tempCookie = '';
            var cookieName = '';
            var cookieValue = '';
            var cookieFound = false;

            for (var i = 0; i < allCookies.length; i++) {
                tempCookie = allCookies[i].split('=');
                cookieName = tempCookie[0].replace(/^\s+|\s+$/g, '');

                if (cookieName === name) {
                    cookieFound = true;
                    if (tempCookie.length > 1) {
                        cookieValue = unescape(tempCookie[1].replace(/^\s+|\s+$/g, ''));
                    }
                    return cookieValue;
                }
                tempCookie = null;
                cookieName = '';
            }
            if (!cookieFound) {
                return null;
            }

            return null;
        }
    });
});
