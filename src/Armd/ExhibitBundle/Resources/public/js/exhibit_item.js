var exhibitItem = {
    objects: {},
    currentId: 0,
    offset: 0,
    stopLoad: false,
    loading: false,
    start: 1,
    activeId: 0,
    init: function(data) { 
        $(function() {
            exhibitItem.currentId = data.id;
            exhibitItem.setExhibits(data.objects, data.offset);
            $('#carousel li img:first').addClass('active');
            
            $(window).load(function() {
                exhibitItem.activeExhibit($('#carousel img.active'));
            });
            /*
            $('#carousel').jcarousel({
                buttonNextCallback: function(carousel, control, flag) {
                    if (!flag) {
                        exhibitItem.loadExhibits();
                    }
                }
            });
            */           

            $('#details-btn').on('click', function() {
                if (!$(this).data('opened')) {
                    $('#exponats-details').show();
                    $('#exponat-main-container').hide();
                    $(this).data('opened', true);
                    $(this).text('Спрятать детали');
                } else {
                    $('#exponats-details').hide();
                    $('#exponat-main-container').show();
                    $(this).data('opened', false);
                    $(this).text('Детали');
                }
                return false;
            });
        });
    },
    setObjectListeners: function() {
        $(".jcarousel-container").mousewheel(function(event, delta) {
            if (delta < 0)
                $(".jcarousel-next").click();
            else if (delta > 0)
                $(".jcarousel-prev").click();
            return false;
        });


        $('#carousel').on('click', 'img', function() {
            exhibitItem.activeExhibit($(this));
        });

        var image, imageWidth, imagePos, popupImage, imgPosLeft, imgPosTop, popupTimeout;

        $('#carousel').on('mouseenter', 'li', function() {
            image = $(this).find('img');
            imagePos = image.offset();
            imgPosLeft = imagePos.left;
            imgPosTop = imagePos.top - 90;
            imageWidth = image.width();
            popupImage = $('<img src="' + image.attr('src') + '" alt="" class="popup-image" style="opacity:0;top:' + imgPosTop + 'px; left:' + imgPosLeft + 'px" id="image_' + image.index() + '" />');
            $('.popup-image').remove();
            $('body').append(popupImage);
            imgPosLeft = imgPosLeft + 20 - popupImage.width() / 2;
            popupImage.css({'left': imgPosLeft, 'opacity': '1', 'z-index': '10'});

        });

        $('#carousel').on('mouseout', 'li', function() {
            $('.popup-image').remove();
        });
    },
    setExhibits: function(objects, offset) {
        var li, imgBorder = $('<i style="width:' + 36 + 'px; height:' + 48 + 'px; "></i>');
        
        if ($.isEmptyObject(objects)) {
            exhibitItem.stopLoad = true;
            
            return false;
        }                
             
        $.extend(exhibitItem.objects, objects);
        exhibitItem.offset = offset;
        
        $('.images-carousel').empty();
        $('.images-carousel').append('<ul id="carousel" class="jcarousel"></ul>');
        
        for (var id in exhibitItem.objects) {
            li = $('<li><img data-id="' + id + '" src="' + exhibitItem.objects[id]['img_thumb'] + '" alt="" data-fullimage="' + exhibitItem.objects[id]['img_big'] + '" /></li>');
            
            if (id == exhibitItem.activeId) {
                li.append(imgBorder);
                li.find('img').addClass('active');
            }
            
            $('#carousel').append(li);           
        }               
        
        $('#carousel').jcarousel({
            start: exhibitItem.start,
            buttonNextCallback: function(carousel, control, flag) {                
                if (!flag) {
                    exhibitItem.start = carousel.last;
                    exhibitItem.loadExhibits();
                }
            }
        });
        
        exhibitItem.setObjectListeners();
    },
    activeExhibit: function(img) {
        var imgBorder = $('<i style="width:' + 36 + 'px; height:' + 48 + 'px; "></i>'),
            fullImageSrc = img.attr('data-fullimage'),
            id = img.attr('data-id'),
            fullImageWidth, fullImageHeight;
            
        exhibitItem.activeId = id;
        img.parent().append(imgBorder).siblings().find('i').remove();
        img.addClass('active').parent().siblings().find('img').removeClass('active');
        
        $('.main-zoomed-image img').css('opacity', 0).attr('src', fullImageSrc);
        
        $('#exp-one-category').text(exhibitItem.objects[id]['museum']['title']);
        $('#exp-one-name').text(exhibitItem.objects[id]['title']);
        $('#exp-one-date').text(exhibitItem.objects[id]['date']);
        
        for (i in exhibitItem.objects[id]['authors']) {
            $('#exp-one-authors').append(exhibitItem.objects[id]['authors'][i]['title'] + ' ');
        }
        
        fullImageWidth = $('.main-zoomed-image img').width();
        fullImageHeight = $('.main-zoomed-image img').height();
        
        $('.main-zoomed-image img').css({
            'opacity': 1,
            'marginLeft': -fullImageWidth / 2,
            'marginTop': -fullImageHeight / 2
        });
    },
    loadExhibits: function() {
        if (exhibitItem.loading || exhibitItem.stopLoad) {
            return false;
        }

        exhibitItem.loading = true;
        armdMk.startLoadingBlock();
        
        var jqxhr = $.ajax({
            url: Routing.generate('armd_load_item_exhibits', {'id': exhibitItem.currentId, 'offset': exhibitItem.offset}),
            type: 'post',
            data: {
                
            },
            dataType: 'json'
        })
        .done(function(data) { 
            exhibitItem.setExhibits(data.objects, data.offset)            
            //$('#carousel').jcarousel('reload');
        })
        .always(function() {
            exhibitItem.loading = false;
            armdMk.stopLoadingBlock();           
        });
        
        return jqxhr;
    }
};