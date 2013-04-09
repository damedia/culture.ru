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
    dx: 0,
    dy: 0,
    resizeStep: 40,
    fullCount: 0,
    init: function(data) {
        $(function() {
            exhibitItem.currentId = data.id;
            exhibitItem.fullCount = data.count;
            exhibitItem.setExhibits(data.objects, data.offset);            
            $('#carousel li img:first').addClass('active');

            $(window).load(function() {
                exhibitItem.activeExhibit($('#carousel img.active'));
            });
            exhibitItem.resizeExhibits();


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
        $(window).bind('resize', function() {
          // exhibitItem.resizeExhibits();
          exhibitItem.activeExhibit($('#carousel img.active'));
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
    getObjectsCount: function() {
        var count = 0;
        
        $.each(exhibitItem.objects, function(key, value) {
            count++;
        });
        
        return count;
    },
    resizeExhibits: function() {
      if($(window).height() > 500){
        var viewportH = $(window).height()-100;
        $('.main-zoomed-image').height(viewportH);
      }
      else{
          $('.main-zoomed-image').height(400);
      }
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
                if (!flag && exhibitItem.getObjectsCount() < exhibitItem.fullCount) {
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
        
        $('.exponats-top-line').css('visibility', 'hidden');
        exhibitItem.activeId = id;
        img.parent().append(imgBorder).siblings().find('i').remove();
        img.addClass('active').parent().siblings().find('img').removeClass('active');

        $('#exp-one-category').text(exhibitItem.objects[id]['museum']['title']);
        $('#exp-one-name').text(exhibitItem.objects[id]['title']);
        $('#exp-one-date').text(exhibitItem.objects[id]['date']);

        exhibitItem.setPrimaryImage(id);
        exhibitItem.setPageData(id);
        $('.exponats-top-line').css('visibility', 'visible');
    },
    setPrimaryImage: function(id) {
        exhibitItem.resizeExhibits();
        var img, nativeImg, imgRealWidth, imgRealHeight,
                imgStartWidth, imgStartHeight,
                src = exhibitItem.objects[id]['img'],
                containerWidth = $('.main-zoomed-image').width(),
                containerHeight = $('.main-zoomed-image').height();

        $('.zoom-thumb').hide();
        $('.main-zoomed-image').empty();
        nativeImg = new Image();
        nativeImg.src = src;
        img = $(nativeImg);
        img.hide();
        img.off();
        $('.zoom-out, .zoom-in').off();
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
                
                $('.zoom-handle').offset({ left: $('.zoom-line').offset().left });
                $('.zoom-handle').draggable({
                    containment: "parent",
                    drag: function( event, ui ) {
                        exhibitItem.zoomHandleAction();
                    }
                });
                
                $('.zoom-out').click(function() {
                    exhibitItem.resizePrimaryImage(exhibitItem.primaryImg.width() - exhibitItem.resizeStep * 2);
                    exhibitItem.setZoomHandle(exhibitItem.primaryImg.width());
                    
                    return false;
                });
                
                $('.zoom-in').click(function() {
                    exhibitItem.resizePrimaryImage(exhibitItem.primaryImg.width() + exhibitItem.resizeStep * 2);
                    exhibitItem.setZoomHandle(exhibitItem.primaryImg.width());
                    
                    return false;
                });
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
            $('.zoom-thumb-border').draggable('destroy');
            $('.zoom-thumb-border').hide();

            img.mousewheel(function(event, delta) {
                if (delta < 0) {
                    exhibitItem.resizePrimaryImage(exhibitItem.primaryImg.width() + exhibitItem.resizeStep);                   
                } else if (delta > 0) {
                    exhibitItem.resizePrimaryImage(exhibitItem.primaryImg.width() - exhibitItem.resizeStep);                   
                } 
                
                exhibitItem.setZoomHandle(exhibitItem.primaryImg.width());

                return false;
            });            
        });
    },
    zoomHandleAction: function() {
        var zhContainer = $('.zoom-line'),
            zh = $('.zoom-handle'),
            zhSize = zhContainer.width() - zh.width(),
            zhPos = zh.offset().left - zhContainer.offset().left;
    
        width = Math.floor(zhPos * (exhibitItem.imgRealWidth - exhibitItem.imgStartWidth) / zhSize + exhibitItem.imgStartWidth);
        exhibitItem.resizePrimaryImage(width);
    },
    setZoomHandle: function(primaryImgWidth) {
        var zhContainer = $('.zoom-line'),
            zh = $('.zoom-handle'),
            zhSize = zhContainer.width() - zh.width();
    
        pos = Math.floor(zhSize * (primaryImgWidth - exhibitItem.imgStartWidth) / (exhibitItem.imgRealWidth - exhibitItem.imgStartWidth));
        
        if (pos < 0) {
            pos = 0;
        } else if (pos > zhContainer.width() - zh.width()) {
            pos = zhContainer.width() - zh.width();
        }
        
        pos = pos + zhContainer.offset().left;
        zh.offset({ left: pos });
    },    
    resizePrimaryImage: function(width) {
        var x, y, dx, dy, x1, y1, x2, y2,
            containerOffset = $('.main-zoomed-image').offset(),
            containerWidth = $('.main-zoomed-image').width(),
            containerHeight = $('.main-zoomed-image').height();
            
        if (width < exhibitItem.imgStartWidth) {
            exhibitItem.primaryImg.width(exhibitItem.imgStartWidth);
            exhibitItem.primaryImg.height(exhibitItem.imgStartHeight);
            exhibitItem.primaryImg.offset(containerOffset);
        } else if (width > exhibitItem.imgRealWidth) {
            exhibitItem.primaryImg.width(exhibitItem.imgRealWidth);
            exhibitItem.primaryImg.height(exhibitItem.imgRealHeight);
        } else {
            exhibitItem.primaryImg.width(width);
            exhibitItem.primaryImg.height(Math.floor((width) * exhibitItem.imgRealHeight / exhibitItem.imgRealWidth));
            
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
        }                  

        exhibitItem.dx = exhibitItem.dy = dx = dy = 0;

        if (exhibitItem.primaryImg.width() < containerWidth) {
            exhibitItem.dx = dx = (containerWidth - exhibitItem.primaryImg.width()) / 2;
            exhibitItem.primaryImg.offset({left: Math.floor(containerOffset.left + dx)});
        } else if (exhibitItem.primaryImg.offset().left > containerOffset.left) {
            exhibitItem.primaryImg.offset({left: containerOffset.left});
        }

        if (exhibitItem.primaryImg.height() < containerHeight) {
            exhibitItem.dy = dy = (containerHeight - exhibitItem.primaryImg.height()) / 2;
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
        exhibitItem.primaryImg.draggable({
            containment: [x1, y1, x2, y2],
            drag: function( event, ui ) {
                exhibitItem.setZoomThumbBorder();
            }
        });
        
        exhibitItem.setZoomThumbBorder();
        
        $('.zoom-thumb-border').css({cursor: 'move'});
        $('.zoom-thumb-border').draggable('destroy');
        $('.zoom-thumb-border').draggable({
            containment: "parent",
            drag: function( event, ui ) {
                exhibitItem.movePrimaryImageByZoomThumb();
            }
        });
    },
    movePrimaryImageByZoomThumb: function() {
        var left, top,
            ztContainer = $('.zoom-thumb-image-block'),
            ztBorder = $('.zoom-thumb-border'),
            container = $('.main-zoomed-image'),
            k = exhibitItem.primaryImg.width() / $('.zoom-thumb-image-block img').width();
            
        left = Math.floor(container.offset().left - (ztBorder.offset().left - ztContainer.offset().left) * k);
        left = (left < container.offset().left ? left : container.offset().left) + exhibitItem.dx;
        top = Math.floor(container.offset().top - (ztBorder.offset().top - ztContainer.offset().top) * k);
        top = (top < container.offset().top ? top : container.offset().top) + exhibitItem.dy;
        
        exhibitItem.primaryImg.offset({left: left, top: top});
    },
    setZoomThumbBorder: function() {
        var left = 0, top = 0, width, height,
            ztb = $('.zoom-thumb-border'),
            ztbImg = $('#zoom-img-bg'),
            container = $('.main-zoomed-image'),
            defaultOffset = ztbImg.offset(),
            k = ztbImg.width() / exhibitItem.primaryImg.width();
        
        ztb.show();
        left = Math.floor((container.offset().left - exhibitItem.primaryImg.offset().left) * k);
        left = left > 0 ? left : 0;
        top = Math.floor((container.offset().top - exhibitItem.primaryImg.offset().top) * k);
        top = top > 0 ? top : 0;
        width = Math.floor(container.width() * k);
        width = width < ztbImg.width() ? width : ztbImg.width();
        height = Math.floor(container.height() * k);
        height = height < ztbImg.height() ? height : ztbImg.height();
        ztb.offset({left: defaultOffset.left + left, top: defaultOffset.top + top});
        ztb.width(width - 3);
        ztb.height(height - 3);
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
        
        if (exhibitItem.objects[id]['museum']['id']) {
            $('#exhibit-museum-img').attr('src', exhibitItem.objects[id]['museum']['img']);
            $('#exhibit-museum-title').text(exhibitItem.objects[id]['museum']['title']);
            
            if (exhibitItem.objects[id]['museum']['address']) {
                $('#exhibit-museum-address').text(exhibitItem.objects[id]['museum']['address']);
            }
            
            if (exhibitItem.objects[id]['museum']['url']) {
                $('#exhibit-museum-url').attr('href', exhibitItem.objects[id]['museum']['url']);
                $('#exhibit-museum-url').text(exhibitItem.objects[id]['museum']['url']);
            }

            if (exhibitItem.objects[id]['museum']['vtour']['url'] != undefined) {
                $('#exhibit-museum-vtour').attr('href', exhibitItem.objects[id]['museum']['vtour']['url']);
                $('#exhibit-museum-vtour').show();
            } else {
                $('#exhibit-museum-vtour').hide();
            }
            
            $('#exhibit-museum').show();
        } else {
            $('#exhibit-museum').hide();
        }
    },
    loadExhibits: function() {
        if (exhibitItem.loading || exhibitItem.stopLoad) {
            return false;
        }

        exhibitItem.loading = true;
        armdMk.startLoadingBlock($('#exponat-main-container'));

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
            armdMk.stopLoadingBlock($('#exponat-main-container'));
        });

        return jqxhr;
    }
};