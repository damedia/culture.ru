var WarArchive = {
    imagesArray: null,
    zoomIcon:'',
	isTouch: document.createTouch !== undefined,
    init: function(icon){
        $(".archive-table tr").click(function(e){
            WarArchive.zoomIcon = icon;
            var index = $(".archive-table tr").index($(this)).toString();
            WarArchive.fancyZoom(WarArchive.imagesArray[index]);
        });

        $.ajax({
            url: '/bundles/armdmuseum/js/war-archive.json',
            dataType: 'json',
            success: function (data) {
                for (var i in data) {
                    if (data.hasOwnProperty(i)) {
                        for (var j in data[i]) {
                            if (data[i].hasOwnProperty(j)) {
                                data[i][j] = {'href' : 'http://culture.ru/ru/uploads/media/war-archive/' + i + '/' + data[i][j]};
                            }
                        }
                    }
                }
                WarArchive.imagesArray = data;
            },
            error: function () {
                alert('Error while loading war-archive.json');
            }
        })
    },

    fancyZoom: function(images) {
        $.fancybox.open(images, {
            autoSize:   false,
            scrolling:  "no",
            openEffect: 'none',
            closeEffect: 'none',
            prevEffect: 'none',
            nextEffect: 'none',
            helpers:  {
                title:  null
            },
            mouseWheel: false,
			afterLoad: function(){
				
			},
            beforeShow: function(){
               $('.fancybox-image').css("visibility", "hidden"); 
			   
			   
			   
            },
            afterShow: function() {
				var $fbWrap = $($.fancybox.wrap),
					
					winWidth = WarArchive.isTouch && window.innerWidth  ? window.innerWidth  : $(window).width(),
					delta = winWidth - $fbWrap.width() - 50,
					fbWrapWidth  = ( delta > 0 ) ? $fbWrap.width() + delta : $fbWrap.width(),
					zWidth = ( delta > 0 ) ? $.fancybox.inner.width() + delta : $.fancybox.inner.width();
				
				
				$('.fancybox-image').css("visibility", "visible");
                $('.fancybox-image').z({
                    width: zWidth,
                    height: $.fancybox.inner.height(),
                    initial_POSITION: "0, 0",
                    initial_ZOOM: $.fancybox.inner.width() / $('.fancybox-image').width() * 100,
                    button_ICON_IMAGE: WarArchive.zoomIcon,
                    border_SIZE: 0,
                });
				
				$fbWrap.css({'left':(winWidth - fbWrapWidth)/2,'width':fbWrapWidth});
				
            },
            onUpdate: function() {
                var $fbInner = $($.fancybox.inner),
                    fbInnerHeight = $fbInner.height(),
					$fbWrap = $($.fancybox.wrap),
					winWidth = $(window).width(),
					winWidth = WarArchive.isTouch && window.innerWidth  ? window.innerWidth  : $(window).width(),
					delta = winWidth - $fbWrap.width() - 50,
					fbWrapWidth  = ( delta > 0 ) ? $fbWrap.width() + delta : $fbWrap.width(),
					fbInnerWidth  = ( delta > 0 ) ? $fbInner.width() + delta : $fbInner.width();

                zoomer_.sW = fbInnerWidth;
                zoomer_.sH = fbInnerHeight;
                zoomer_.zoom = $.fancybox.inner.width() / $('.fancybox-image').width() * 100;
                zoomer_.$holder.width(fbInnerWidth);
                zoomer_.$holder.height(fbInnerHeight);
                zoomer_.Reset();
				
				$fbWrap.css({'left':(winWidth - fbWrapWidth)/2,'width':fbWrapWidth});
            }
        });
    }
    
}