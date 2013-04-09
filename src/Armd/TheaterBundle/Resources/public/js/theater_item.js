var armdMkTheaterItem = {
    theaterId: 0,
    visibleCount: 0,
    loadByCount: 12,


    init: function (theaterId) {
        $(function() {      
            armdMkTheaterItem.refreshVisibleCount();
            armdMkTheaterItem.theaterId = theaterId;
            $('.fancybox').fancybox({
                autoResize: true,
                prevEffect: 'none',
                nextEffect: 'none'
            });
                       
            $('.content-tabs').on('click', 'a', function(){
                var tabId = $(this).attr('href');
                $(this).addClass('active')
                        .siblings().removeClass('active');
                $(tabId).show().siblings('.content-tab').hide();        
                return false;
            });

            $('.sub-tabs').on('click', 'a', function(){
                var tabId = $(this).attr('href');
                $(this).addClass('active')
                        .siblings().removeClass('active');
                $(tabId).show().siblings('.sub-tab').hide();        
                return false;
            });
            
            $('#show-more').bind('click', function(event) {
                event.preventDefault();
                armdMkTheaterItem.loadList(true);
            });
        });
        
        
    },

    startLoading: function () {
        armdMk.startLoadingBlock('body');
        $('#search-russia-images-button').addClass('loading');
    },

    stopLoading: function () {
        armdMk.stopLoadingBlock('body');
        $('#search-russia-images-button').removeClass('loading');
    },

    refreshVisibleCount: function() {
        armdMkTheaterItem.visibleCount = $('.plitka-one-wrap').length;
    },

    loadList: function (append) {        
        var offset = append ? armdMkTheaterItem.visibleCount : 0;
        
        armdMkTheaterItem.startLoading();
        
        var jqxhr = $.ajax({
            url: Routing.generate('armd_theater_performance_list_data'),
            type: 'get',
            cache: false,            
            dataType: 'html',
            data: {
                'theater_id': armdMkTheaterItem.theaterId,
                'offset': offset,
                'limit': armdMkTheaterItem.loadByCount
            }
        })
        .done(function(data) { 
            var count = $(data).filter('.plitka-one-wrap').length;
            
            if (append) {
                if (count) {
                    $('#performance-container').append(data);
                }
            }
            else {
                $('#performance-container').html(data);
            }
            
            if (!count || count < armdMkTheaterItem.loadByCount) {
                $('.more').hide();
            } else {
                $('.more').show();
            }
            
            armdMkTheaterItem.refreshVisibleCount();           
        })
        .always(function() {
            armdMkTheaterItem.stopLoading();           
        });
        
        return jqxhr;
    }
};
