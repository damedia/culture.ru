$(function () {
    $('.select2-armd-tags').select2({
        width: '450px',
        initSelection: function (element, callback) {
            var data = [];
            $(element.val().split(",")).each(function () {
                data.push({id: this, text: this});
            });
            callback(data);
        },
        multiple: true,
        createSearchChoice : function (term) { return {id: term, text: term}; },
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: Routing.generate('armd_tag_get_tags'),
            dataType: 'json',
            quietMillis: 300,
            data: function (term, page) {
                return {
                    'term': term, // search term
                };
            },
            results: function (data, page) { // parse the results into the format expected by Select2.
                var result = {results: []};
                for (i in data) {
                    result.results.push({'id': data[i].name, 'text': data[i].name});
                }
                return result;
            }
        }


//        query: function(query) {
//            var data = {
//                results: []
//            };
//            data.results.push({
//                id: query.term,
//                text: query.term
//            });
//
//            if (query.term.length > 3) {
//                $.ajax({
//                    url: Routing.generate('armd_tag_get_tags'),
//                    data: {
//                        'term': query.term
//                    },
//                    type: 'get',
//                    dataType: 'json',
//                    success:  function(ajaxData) {
//                        for (i in ajaxData) {
//                            data.results.push({'id': ajaxData[i].name, 'text': ajaxData[i].name});
//                        }
//                        query.callback(data);
//                    },
//                    complete: function(ajaxData) {
//                        query.callback(data);
//                    }
//                });
//            }
//        }

    });
    
    /*MENU FIX*/
    
    var menuFix = {
        navHeight: $('.navbar-fixed-top').height(),
        delay: (function(){
          var timer = 0;
          return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
          };
        })(),
        
        init: function(){
            this.resizeMenu();
            this.updateMenu();
        },
        resizeMenu: function(){
            if ($('body').width() >= 979) {
                $('body').css('paddingTop', this.navHeight+34);
            } else {
                $('body').css('paddingTop', 0);
            }
        },
        updateMenu: function(){
            var self = this;
            $(window).resize(function() {
                self.delay(function(){
                    var h = $('.navbar-fixed-top').height();
                    if (h != self.navHeight) {
                        self.navHeight = h;
                        self.resizeMenu();
                    }
                }, 100);
            });
        }
        
    }
    
    if ($('.navbar-fixed-top').length > 0) {
       menuFix.init();
    }
});
