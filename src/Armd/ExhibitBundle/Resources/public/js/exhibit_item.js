var exhibitItem = {
    objects: {},
    currentId: 0,
    offset: 0,
    stopLoad: false,
    loading: false,
    start: 1,
    activeId: 0,
    primaryImg: null,
    imgRealWidth: 0,
    imgRealHeight: 0,
    imgStartWidth: 0,
    imgStartHeight: 0,
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
            li = $('<li><img data-id="' + id + '" src="' + exhibitItem.objects[id]['img_thumb'] + '" alt="" /></li>');

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
                id = img.attr('data-id');

        exhibitItem.activeId = id;
        img.parent().append(imgBorder).siblings().find('i').remove();
        img.addClass('active').parent().siblings().find('img').removeClass('active');

        $('#exp-one-category').text(exhibitItem.objects[id]['museum']['title']);
        $('#exp-one-name').text(exhibitItem.objects[id]['title']);
        $('#exp-one-date').text(exhibitItem.objects[id]['date']);

        exhibitItem.setPrimaryImage(id);
        exhibitItem.setPageData(id);
    },
    setPrimaryImage: function(id) {
        var img, nativeImg, imgRealWidth, imgRealHeight,
                imgStartWidth, imgStartHeight,
                wheelStep = 40, src = exhibitItem.objects[id]['img'],
                containerWidth = $('.main-zoomed-image').width(),
                containerHeight = $('.main-zoomed-image').height();

        $('.zoom-thumb').hide();
        $('.main-zoomed-image').empty();
        nativeImg = new Image();
        nativeImg.src = src;
        img = $(nativeImg);
        img.hide();
        img.off();
        $('.main-zoomed-image').append(img);
        exhibitItem.primaryImg = img;
        img.load(function() {
            exhibitItem.imgRealWidth = imgRealWidth = nativeImg.width;
            exhibitItem.imgRealHeight = imgRealHeight = nativeImg.height;

            if (imgRealWidth > containerWidth || imgRealHeight > containerHeight) {
                imgStartHeight = Math.floor(containerWidth * imgRealHeight / imgRealWidth);

                if (imgStartHeight <= containerHeight) {
                    imgStartWidth = containerWidth;
                } else {
                    imgStartHeight = containerHeight;
                    imgStartWidth = Math.floor(containerHeight * imgRealWidth / imgRealHeight);
                }

                img.css({cursor: 'move'});

                $('.zoom-thumb img').width(200);
                $('.zoom-thumb img').attr('src', src);
                $('.zoom-thumb').show();
            } else {
                imgStartWidth = imgRealWidth;
                imgStartHeight = imgRealHeight;
            }

            img.width(imgStartWidth);
            img.height(imgStartHeight);
            exhibitItem.imgStartWidth = imgStartWidth;
            exhibitItem.imgStartHeight = imgStartHeight;

            if (img.width() < containerWidth) {
                img.offset({left: Math.floor((containerWidth - img.width()) / 2)});
            }

            if (img.height() < containerHeight) {
                img.offset({top: Math.floor((containerHeight - img.height()) / 2)});
            }

            img.show();

            img.mousewheel(function(event, delta) {
                if (delta < 0) {
                    exhibitItem.resizePrimaryImage(wheelStep);                   
                } else if (delta > 0) {
                    exhibitItem.resizePrimaryImage(- wheelStep);                   
                }              

                return false;
            });
            
            exhibitItem.setZoomThumbBorder();
        });
    },
    resizePrimaryImage: function(value) {
        var x, y, dx, dy, x1, y1, x2, y2,
            imgWidth = exhibitItem.primaryImg.width(),
            containerOffset = $('.main-zoomed-image').offset(),
            containerWidth = $('.main-zoomed-image').width(),
            containerHeight = $('.main-zoomed-image').height();

        if (value > 0) {
            if (imgWidth + value < exhibitItem.imgRealWidth) {
                exhibitItem.primaryImg.width(imgWidth + value);
                exhibitItem.primaryImg.height(Math.floor((imgWidth + value) * exhibitItem.imgRealHeight / exhibitItem.imgRealWidth));
            } else {
                exhibitItem.primaryImg.width(exhibitItem.imgRealWidth);
                exhibitItem.primaryImg.height(exhibitItem.imgRealHeight);
            }
        } else if (value < 0) {
            if (imgWidth + value > exhibitItem.imgStartWidth) {
                exhibitItem.primaryImg.width(imgWidth + value);
                exhibitItem.primaryImg.height(Math.floor((imgWidth + value) * exhibitItem.imgRealHeight / exhibitItem.imgRealWidth));

                if (exhibitItem.primaryImg.offset().left + exhibitItem.primaryImg.width() < containerOffset.left + containerWidth) {
                    x = containerOffset.left + containerWidth - exhibitItem.primaryImg.width();
                    x = x > containerOffset.left ? containerOffset.left : x;
                } else {
                    x = exhibitItem.primaryImg.offset().left;
                }

                if (exhibitItem.primaryImg.offset().top + exhibitItem.primaryImg.height() < containerOffset.top + containerHeight) {
                    y = containerOffset.top + containerHeight - exhibitItem.primaryImg.height();
                    y = y > containerOffset.top ? containerOffset.top : y;
                } else {
                    y = exhibitItem.primaryImg.offset().top;
                }

                exhibitItem.primaryImg.offset({left: x, top: y});
            } else {
                exhibitItem.primaryImg.width(exhibitItem.imgStartWidth);
                exhibitItem.primaryImg.height(exhibitItem.imgStartHeight);
                exhibitItem.primaryImg.offset(containerOffset);
            }
        }

        dx = dy = 0;

        if (exhibitItem.primaryImg.width() < containerWidth) {
            dx = (containerWidth - exhibitItem.primaryImg.width()) / 2;
            exhibitItem.primaryImg.offset({left: Math.floor(containerOffset.left + dx)});
        } else if (exhibitItem.primaryImg.offset().left > containerOffset.left) {
            exhibitItem.primaryImg.offset({left: containerOffset.left});
        }

        if (exhibitItem.primaryImg.height() < containerHeight) {
            dy = (containerHeight - exhibitItem.primaryImg.height()) / 2;
            exhibitItem.primaryImg.offset({top: Math.floor(containerOffset.top + dy)});
        } else if (exhibitItem.primaryImg.offset().top > containerOffset.top) {
            exhibitItem.primaryImg.offset({top: containerOffset.top});
        }

        x1 = containerOffset.left + containerWidth - exhibitItem.primaryImg.width();
        x1 = (x1 > containerOffset.left ? containerOffset.left : x1) + dx;
        y1 = containerOffset.top + containerHeight - exhibitItem.primaryImg.height();
        y1 = (y1 > containerOffset.top ? containerOffset.top : y1) + dy;
        x2 = containerOffset.left + dx;
        y2 = containerOffset.top + dy;
        exhibitItem.primaryImg.draggable('destroy');
        exhibitItem.primaryImg.draggable({containment: [x1, y1, x2, y2]});
        
        exhibitItem.setZoomThumbBorder();

        return false;
    },
    setZoomThumbBorder: function() {
        var left = 0, top = 0, width, height,
            ztb = $('.zoom-thumb-border'),
            ztbImg = $('.zoom-thumb-image-block img'),
            container = $('.main-zoomed-image'),
            defaultOffset = $('.zoom-thumb-image-block img').offset(),
            k = ztbImg.width() / exhibitItem.primaryImg.width();
        
        if (exhibitItem.primaryImg.offset().left < 0) {
            left = Math.floor((container.offset().left + exhibitItem.primaryImg.offset().left) * k);
        }
              
        if (exhibitItem.primaryImg.offset().top < 0) {
            top = Math.floor((container.offset().top + exhibitItem.primaryImg.offset().top) * k);
        }
        width = Math.floor(container.width() * k);
        width = width < ztbImg.width() ? width : ztbImg.width();
        height = Math.floor(container.height() * k);
        height = height < ztbImg.height() ? height : ztbImg.height();
        ztb.offset({left: defaultOffset.left + left, top: defaultOffset.top + top});
        ztb.width(width - 3);
        ztb.height(height - 3);
        console.log(ztb.offset());
    },
    setPageData: function(id) {
        var vBlock = $('#exhibit-video');

        $('#exp-one-category').text(exhibitItem.objects[id]['museum']['title']);
        $('.exhibit-title').text(exhibitItem.objects[id]['title']);
        $('.exhibit-date').text(exhibitItem.objects[id]['date']);
        $('.exhibit-authors').empty();

        for (var i in exhibitItem.objects[id]['authors']) {
            if (i != 0) {
                $('.exhibit-authors').append(', ');
            }

            $('.exhibit-authors').append(exhibitItem.objects[id]['authors'][i]['title']);
        }

        $('#exhibit-description').text(exhibitItem.objects[id]['description']);
        $('#exhibit-detail-img').attr('src', exhibitItem.objects[id]['img']);

        if (exhibitItem.objects[id]['video']['frame'] != undefined) {
            vBlock.find('iframe').attr('src', exhibitItem.objects[id]['video']['frame'] + '&dopparam=armada_skin');
            vBlock.find('img').attr('src', exhibitItem.objects[id]['video']['img']);
            vBlock.find('.fancybox').fancybox({
                autoResize: true,
                prevEffect: 'none',
                nextEffect: 'none'
            });
            vBlock.show();
        } else {
            vBlock.hide();
        }

        $('#exhibit-museum-img').attr('src', exhibitItem.objects[id]['museum']['img']);
        $('#exhibit-museum-title').text(exhibitItem.objects[id]['museum']['title']);
        $('#exhibit-museum-address').text(exhibitItem.objects[id]['museum']['address']);
        $('#exhibit-museum-url').attr('href', exhibitItem.objects[id]['museum']['url']);
        $('#exhibit-museum-url').text(exhibitItem.objects[id]['museum']['url']);

        if (exhibitItem.objects[id]['museum']['vtour']['url'] != undefined) {
            $('#exhibit-museum-vtour').attr('href', exhibitItem.objects[id]['museum']['vtour']['url']);
            $('#exhibit-museum-vtour').show();
        } else {
            $('#exhibit-museum-vtour').hide();
        }
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