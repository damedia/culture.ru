var WarArchive = {
    imagesArray: null,
    zoomIcon:'',

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
            beforeShow: function(){
               $('.fancybox-image').css("visibility", "hidden"); 
            },
            afterShow: function() {
                $('.fancybox-image').css("visibility", "visible");
                $('.fancybox-image').z({
                    width: $.fancybox.inner.width(),
                    height: $.fancybox.inner.height(),
                    initial_POSITION: "0, 0",
                    initial_ZOOM: $.fancybox.inner.width() / $('.fancybox-image').width() * 100,
                    button_ICON_IMAGE: WarArchive.zoomIcon,
                    border_SIZE: 0,
                });
            },
            onUpdate: function() {
                var $fbInner = $($.fancybox.inner),
                    fbInnerWidth  = $fbInner.width(),
                    fbInnerHeight = $fbInner.height();

                zoomer_.sW = fbInnerWidth;
                zoomer_.sH = fbInnerHeight;
                zoomer_.zoom = $.fancybox.inner.width() / $('.fancybox-image').width() * 100
                zoomer_.$holder.width(fbInnerWidth);
                zoomer_.$holder.height(fbInnerHeight);

                zoomer_.Reset();
            }
        });
    }
    
}