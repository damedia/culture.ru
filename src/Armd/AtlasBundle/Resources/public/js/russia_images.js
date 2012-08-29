var armdRussiaImages = {
    inputTimeout: null,

    init:function () {
        $.manageAjax.create('lifo', {queue:'clear', maxRequests:2, abortOld:true});

        // search
        armdRussiaImages.afterDelayedEvent('keyup', '#search_text', 1000, function () {
            armdRussiaImages.loadList();
        });

        armdRussiaImages.initUi();
    },

    initUi:function () {


        $('#images-of-russia-container').on('mouseenter', '.obrazy .rusObr-list-one-wrap', function () {
            var width = $(this).width(),
                height = $(this).height(),
                liMas = $(this).parents('ul').find('li'),
                thisli = $(this).closest('li');

            $(this).addClass('rusHovered');
            $(this).parent().css('height', height);
            //.css({'width':width*2-1,'height':height});
            $(this).find('.rusObr-list-one').css({'width':width - 51, 'height':height - 50});
            //$(this).find('.rusHovered-contacts').css({'width':width -54,'height':height - 50 });
            if (liMas.index(thisli) % 4 == 3) {
                $(this).find('.rusHovered-contacts').css({'left':-247}).addClass('left-hand');
                $(this).find('.rusObr-list-one').css({'left':0});
                //$(this).css({'left':-width});
            }
            else {
                $(this).find('.rusHovered-contacts').css({'left':width - 1});
            }
            ;

        });

        $('#images-of-russia-container').on('mouseleave', '.obrazy .rusObr-list-one-wrap', function () {
            $(this).removeClass('rusHovered').css({'width':'auto', 'height':'auto', 'left':0});
            $(this).find('.rusObr-list-one').css({'width':'auto', 'height':'auto', 'left':0});
            //$(this).find('.rusHovered-contacts').css({'width':'auto','height':'auto'});
            $(this).parent().css({'height':'auto'});
        });

        $('#search_text').bind('focus', function(){
           if($(this).val() === 'Поиск по объектам') {
               $(this).val('');
           }
        });

        $('#search_text').bind('blur', function(){
           if($(this).val().length == 0) {
               $(this).val('Поиск по объектам');
           }
        });

        $('.obr-submit-input').bind('click', function() {
            armdRussiaImages.loadList();
        })

    },

    startLoading:function () {
        $('#images-of-russia-loading').show();
    },

    stopLoading:function () {
        $('#images-of-russia-loading').hide();
    },

    afterDelayedEvent:function afterDelayedEvent(eventtype, selector, delay, action) {
        $(selector).bind(eventtype, function () {
            if (typeof(armdRussiaImages.inputTimeout) != "undefined") {
                clearTimeout(armdRussiaImages.inputTimeout);
            }
            armdRussiaImages.inputTimeout = setTimeout(action, delay);
        });
    },

    loadList:function () {
        armdRussiaImages.startLoading();
        $.manageAjax.clear('lifo', true);
        $.manageAjax.add('lifo', {
            url:Routing.generate('armd_atlas_russia_images_list'),
            cache:false,
            dataType:'html',
            type:'POST',
            data:{
                searchString:$('#search_text').val()
            },
            success:function (data) {
                $('#images-of-russia-container').html(data);
                armdRussiaImages.stopLoading();
            },
        });
    }
};