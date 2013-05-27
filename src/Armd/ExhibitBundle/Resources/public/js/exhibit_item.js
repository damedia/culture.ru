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
    resizeStepPercent: 20,
    fullCount: 0,
    touches: [],
    dragX1: null,
    dragY1: null,
    dragX2: null,
    dragY2: null,
    init: function(data) {
        exhibitItem.objects = {};
        exhibitItem.offset = 0;
        exhibitItem.stopLoad = false;
        exhibitItem.loading = false;
        exhibitItem.start = 1;
        exhibitItem.activeId = 0;
        exhibitItem.currentId = data.id;
        exhibitItem.fullCount = data.count;
        exhibitItem.setExhibits(data.objects, data.offset);            
        $('#carousel li img:first').addClass('active');

        exhibitItem.activeExhibit($('#carousel img.active'));
        exhibitItem.resizeExhibits();


        $('#details-btn').off('click').on('click', function() {
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
    resetActiveExhibit: function() {
        exhibitItem.dragX1 = exhibitItem.dragY1 = exhibitItem.dragX2 = exhibitItem.dragY2 = null;
    },
    activeExhibit: function(img) {
        var imgBorder = $('<i style="width:' + 36 + 'px; height:' + 48 + 'px; "></i>'),
                id = img.attr('data-id');
        
        exhibitItem.resetActiveExhibit();
        
        $('.exponats-top-line').css('visibility', 'hidden');
        exhibitItem.activeId = id;
        img.parent().append(imgBorder).siblings().find('i').remove();
        img.addClass('active').parent().siblings().find('img').removeClass('active');       

        exhibitItem.setPrimaryImage(id);
        exhibitItem.setPageData(id);
        $('.exponats-top-line').css('visibility', 'visible');
    },
    setPrimaryImage: function(id) {
        armdMk.startLoadingBlock($('#exponat-main-container'));
        exhibitItem.resizeExhibits();
        var img, nativeImg, imgRealWidth, imgRealHeight,
                imgStartWidth, imgStartHeight,
                containerWidth = $('.main-zoomed-image').width(),
                containerHeight = $('.main-zoomed-image').height();

        $('.zoom-thumb').hide();
        $('.main-zoomed-image').empty();
        nativeImg = new Image();
        nativeImg.src = exhibitItem.objects[id]['img'];
        img = $(nativeImg);
        img.hide();
        img.off();
        $('.main-zoomed-image, .main-zoomed-image img, .zoom-thumb, .zoom-out, .zoom-in').off();
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
                $('.zoom-thumb img').attr('src', exhibitItem.objects[id]['img_small']);
                $('.zoom-thumb').show();
                
                $('.zoom-handle').offset({ left: $('.zoom-line').offset().left });
                $('.zoom-handle').draggable({
                    containment: "parent",
                    drag: function( event, ui ) {
                        exhibitItem.zoomHandleAction();
                    }
                });
                
                $('.zoom-out').click(function() {
                    exhibitItem.resizePrimaryImage(exhibitItem.primaryImg.width() - exhibitItem.getDeltaWidth());
                    exhibitItem.setZoomHandle(exhibitItem.primaryImg.width());
                    
                    return false;
                });
                
                $('.zoom-in').click(function() {
                    exhibitItem.resizePrimaryImage(exhibitItem.primaryImg.width() + exhibitItem.getDeltaWidth());
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

            $('.main-zoomed-image, .zoom-thumb').mousewheel(function(event, delta) {
                if (delta < 0) {
                    exhibitItem.resizePrimaryImage(exhibitItem.primaryImg.width() + exhibitItem.getDeltaWidth());                   
                } else if (delta > 0) {
                    exhibitItem.resizePrimaryImage(exhibitItem.primaryImg.width() - exhibitItem.getDeltaWidth());                   
                } 
                
                exhibitItem.setZoomHandle(exhibitItem.primaryImg.width());

                return false;
            });
            
            $('.main-zoomed-image img')
                .on("touchmove", function (event) {
                    event.preventDefault();
                    
                    var x0 = event.originalEvent.touches[0].pageX,
                        y0 = event.originalEvent.touches[0].pageY,
                        x0old = exhibitItem.touches[0].pageX,
                        y0old = exhibitItem.touches[0].pageY;                       

                    if (event.originalEvent.touches.length === 2) {
                        // zoom
                        var x1 = event.originalEvent.touches[1].pageX,
                            y1 = event.originalEvent.touches[1].pageY,
                            x1old = exhibitItem.touches[1].pageX,
                            y1old = exhibitItem.touches[1].pageY,
                            delta = Math.sqrt(Math.abs(x0 - x1) * Math.abs(x0 - x1) + Math.abs(y0 - y1) * Math.abs(y0 - y1)),
                            deltaOld = Math.sqrt(Math.abs(x0old - x1old) * Math.abs(x0old - x1old) + Math.abs(y0old - y1old) * Math.abs(y0old - y1old));
                        
                        if (Math.abs(delta - deltaOld) > 10) {
                            if (delta > deltaOld) {
                                exhibitItem.resizePrimaryImage(exhibitItem.primaryImg.width() + exhibitItem.getDeltaWidth() / 2);
                                exhibitItem.setZoomHandle(exhibitItem.primaryImg.width());
                            } else if (delta < deltaOld) {
                                exhibitItem.resizePrimaryImage(exhibitItem.primaryImg.width() - exhibitItem.getDeltaWidth() / 2);
                                exhibitItem.setZoomHandle(exhibitItem.primaryImg.width());
                            }
                            
                            exhibitItem.touches = $.extend(true, {}, event.originalEvent.touches);
                        }                                             
                    } else if (event.originalEvent.touches.length === 1) {
                        // drag
                        
                        if (Math.abs(x0 - x0old) > 10 || Math.abs(y0 - y0old) > 10) {
                            exhibitItem.touchDragPrimaryImage(x0, y0, x0old, y0old);
                            exhibitItem.touches = $.extend(true, {}, event.originalEvent.touches);                           
                        }
                    }                                   
                    
                    return false;
                })
                .on("touchstart", function (event) {
                    event.preventDefault();
                    
                    exhibitItem.touches = $.extend(true, {}, event.originalEvent.touches);
                    
                    return false;
                });
                
            armdMk.stopLoadingBlock($('#exponat-main-container'));
        });
    },
    getDeltaWidth: function() {
        return Math.floor(exhibitItem.primaryImg.width() * exhibitItem.resizeStepPercent / 100);
    },
    touchDragPrimaryImage: function(x, y, xOld, yOld) {
        var dx = x - xOld,
            dy = y - yOld,
            left = exhibitItem.primaryImg.offset().left,
            top = exhibitItem.primaryImg.offset().top,
            newLeft, newTop;
    
        if ((dx == 0 && dy == 0) || exhibitItem.dragX1 == null) {
            return;
        }
        
        if (left + dx > exhibitItem.dragX1) {
            if (left + dx < exhibitItem.dragX2) {
                newLeft = left + dx;
            } else {
                newLeft = exhibitItem.dragX2;
            }
        } else {
            newLeft = exhibitItem.dragX1;
        }
        
        if (top + dy > exhibitItem.dragY1) {
            if (top + dy < exhibitItem.dragY2) {
                newTop = top + dy;
            } else {
                newTop = exhibitItem.dragY2;
            }
        } else {
            newTop = exhibitItem.dragY1;
        }
        
        exhibitItem.primaryImg.offset({ left: newLeft, top: newTop });
        exhibitItem.setZoomThumbBorder();
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
        var x, y, dx, dy, x1, y1, x2, y2, k,
            containerOffset = $('.main-zoomed-image').offset(),
            containerWidth = $('.main-zoomed-image').width(),
            containerHeight = $('.main-zoomed-image').height(),
            oldWidth = exhibitItem.primaryImg.width(),
            oldHeight = exhibitItem.primaryImg.height();
    
        width = Math.floor(width);
            
        if (width < exhibitItem.imgStartWidth) {
            exhibitItem.primaryImg.width(exhibitItem.imgStartWidth);
            exhibitItem.primaryImg.height(exhibitItem.imgStartHeight);
            exhibitItem.primaryImg.offset(containerOffset);
        } else {
            if (width > exhibitItem.imgRealWidth) {
                exhibitItem.primaryImg.width(exhibitItem.imgRealWidth);
                exhibitItem.primaryImg.height(exhibitItem.imgRealHeight);
            } else {
                exhibitItem.primaryImg.width(width);
                exhibitItem.primaryImg.height(Math.floor((width) * exhibitItem.imgRealHeight / exhibitItem.imgRealWidth));
            }
            
            k = oldWidth - containerWidth <= 0 ? 0.5 : (containerOffset.left - exhibitItem.primaryImg.offset().left + containerWidth / 2) / oldWidth;
            x = Math.floor(exhibitItem.primaryImg.offset().left - (exhibitItem.primaryImg.width() - oldWidth) * k);
            
            if (x + exhibitItem.primaryImg.width() < containerOffset.left + containerWidth) {
                x = containerOffset.left + containerWidth - exhibitItem.primaryImg.width();
                x = x > containerOffset.left ? containerOffset.left : x;
            }

            k = oldHeight - containerHeight <= 0 ? 0.5 : (containerOffset.top - exhibitItem.primaryImg.offset().top + containerHeight / 2) / oldHeight;           
            y = Math.floor(exhibitItem.primaryImg.offset().top - (exhibitItem.primaryImg.height() - oldHeight) * k);
            
            if (y + exhibitItem.primaryImg.height() < containerOffset.top + containerHeight) {
                y = containerOffset.top + containerHeight - exhibitItem.primaryImg.height();
                y = y > containerOffset.top ? containerOffset.top : y;
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
        exhibitItem.dragX1 = x1 = (x1 > containerOffset.left ? containerOffset.left : x1) + dx;
        y1 = containerOffset.top + containerHeight - exhibitItem.primaryImg.height();
        exhibitItem.dragY1 = y1 = (y1 > containerOffset.top ? containerOffset.top : y1) + dy;
        exhibitItem.dragX2 = x2 = containerOffset.left + dx;
        exhibitItem.dragY2 = y2 = containerOffset.top + dy;
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
        var authorClone, authorBlank = $('#author-blank'),
            vBlock = $('#exhibit-video');

        if (exhibitItem.objects[id]['museum']['title']) {
            $('#exp-one-category').text(exhibitItem.objects[id]['museum']['title']);
            $('#exp-one-category').show();
        } else {
            $('#exp-one-category').hide();
        }
        
        $('#exp-one-name').text(exhibitItem.objects[id]['title']);        
        $('.exhibit-title').text(exhibitItem.objects[id]['title']);
        
        if (exhibitItem.objects[id]['date']) {
            $('#exp-one-date').text(exhibitItem.objects[id]['date']);
            $('.exhibit-date').text(exhibitItem.objects[id]['date']);
        }
        
        $('.exhibit-authors').empty();

        for (var i in exhibitItem.objects[id]['authors']) {
            if (i != 0) {
                $('.exhibit-authors').append(', ');
            }

            $('.exhibit-authors').append(exhibitItem.objects[id]['authors'][i]['title']);
            
            if (exhibitItem.objects[id]['description']) {                
                $('.author-block:visible').remove();
                authorClone = authorBlank.clone();  
                authorClone.find('img').attr('src', exhibitItem.objects[id]['authors'][i]['image']);
                authorClone.find('.author-block-title').text(exhibitItem.objects[id]['authors'][i]['title']);
                
                if (exhibitItem.objects[id]['authors'][i]['life_dates']) {
                    authorClone.find('.author-block-title').text(authorClone.find('.author-block-title').text() + ', ' + exhibitItem.objects[id]['authors'][i]['life_dates']);
                }
                
                authorClone.find('.author-block-body').text(exhibitItem.objects[id]['authors'][i]['description']);
                authorClone.show();
                authorBlank.before(authorClone);                
            }
        }

        if (exhibitItem.objects[id]['description']) {
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

                $('#exhibit-museum').show();
            } else {
                $('#exhibit-museum').hide();
            }
            
            if (exhibitItem.objects[id]['virtual_tour']) {
                $('#exhibit-virtual-tour').find('a').attr('href', exhibitItem.objects[id]['virtual_tour']['url']);
                $('#exhibit-virtual-tour').find('img').attr('src', exhibitItem.objects[id]['virtual_tour']['img']);
                $('#exhibit-virtual-tour').show();
            } else {
                $('#exhibit-virtual-tour').hide();
            }
            
            $('#details-btn').show();
        } else {
            $('#details-btn').hide();
        }
    },
    loadExhibits: function() {
        if (exhibitItem.loading || exhibitItem.stopLoad) {
            return false;
        }

        exhibitItem.loading = true;
        armdMk.startLoadingBlock($('#exponat-main-container'));

        var jqxhr = $.ajax({
            url: Routing.generate('armd_load_item_objects', {'id': exhibitItem.currentId, 'offset': exhibitItem.offset}),
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