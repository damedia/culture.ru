var armdMuseums = {

    init: function () {
        armdMuseums.initUi();
    },

    initUi: function () {
        $('#museums-filter-form').ajaxForm({
            beforeSubmit: function(){
                armdMk.startLoading();
                armdMuseums.resetTextFilter();
                armdMuseums.startLoading();
            },
            success: function(data) {
                $('#museums-container').html(data);
                $('#category-chooser a.active').trigger('click');
                armdMuseums.stopLoading();
                armdMk.stopLoading();
            }
        });

        armdMuseums.initLoadedUi();
        
        // init search
        $('#search-form').bind('submit', function(event) {
            if ($('#search-this-section').prop('checked')) {
                event.preventDefault();
                armdMuseums.loadTextFilterResult();
            }
        });
    },

    initLoadedUi: function() {
        
       $('.museum-image, .plitka-name').on('click', $(this), function(){
		
        var block = $(this).closest('.plitka-one-wrap'),
            newBlock,
            blockPosTop = 0,
            blockPosLeft = 0;
            
        blockPosTop = block.position().top;
        blockPosLeft = block.position().left;
        
        block.find('.museum-image').animate({
                height: '274px'
            }, 400, function(){
                newBlock = block
                                .clone()
                                    .addClass('plitka-one-wrap-opened')
                                    .attr('id', 'newBlock')
                                    .css({'position':'absolute', 'top':blockPosTop, 'left':blockPosLeft, height:'291px'});
                
                $('#museums-container').append('<div class="overlay museum-overlay"></div>')
                                       .append(newBlock);
                
                block.css('visibility', 'hidden');
               
                $('.museum-overlay').on('click', $(this), function(){
                    newBlock.remove();
                    $(this).remove();
                    
                    block.css({'visibility': 'visible'})
                         .find('.museum-image').animate({'height':'78px'}, 400);
                })
            });

            
            
            
        })

        if ($('.vob').length) {
            $('.vob').fancybox({
                'autoScale': false,
                'autoDimensions': false,
                'padding': 0,
                'margin': 0,
                'fitToView': true
            });
        }

        $(".iframe").fancybox({
            'width': '100%',
            'height': '100%',
            'autoScale': true,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'
        });
    },

    resetFilter: function() {
        $('#filter-region').val('').selectgroup('refresh');
        $('#filter-category').val('').selectgroup('refresh');
    },

    resetTextFilter: function() {
        $('#search-txt').val('').selectgroup('refresh');
    },

    startLoading: function () {
        $('#museums-loading').show();
    },

    stopLoading: function () {
        $('#museums-loading').hide();
    },

    loadTextFilterResult: function() {
        var searchQuery = $('#search-txt').val().trim();
        if (searchQuery.length > 0) {
            armdMuseums.resetFilter();
            armdMk.startLoading();
            $.ajax({
                url: Routing.generate('armd_museum_list'),
                data: {
                    'search_query': searchQuery
                },
                dataType: 'html',
                method: 'get',
                success: function(data) {
                    $('#museums-container').html(data);
                    $('#category-chooser a.active').trigger('click');
                },
                complete: function() {
                    armdMk.stopLoading();
                }
            })
        }
    }

};
