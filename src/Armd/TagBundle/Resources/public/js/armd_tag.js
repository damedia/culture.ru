$(function() {
    $('.select2-armd-tags').select2({
        width: '450px',
        initSelection : function (element, callback) {
            var data = [];
            $(element.val().split(",")).each(function () {
                data.push({id: this, text: this});
            });
            callback(data);
        },
        multiple: true,
        query: function(query) {
            var data = {
                results: []
            };
            data.results.push({
                id: query.term,
                text: query.term
            });

            if (query.term.length > 3) {
                $.ajax({
                    url: Routing.generate('armd_tag_get_tags'),
                    data: {
                        'term': query.term
                    },
                    type: 'get',
                    dataType: 'json',
                    success:  function(ajaxData) {
                        for (i in ajaxData) {
                            data.results.push({'id': ajaxData[i].name, 'text': ajaxData[i].name});
                        }
                        query.callback(data);
                    },
                    complete: function(ajaxData) {
                        query.callback(data);
                    }
                });
            }

        }
    });
});
