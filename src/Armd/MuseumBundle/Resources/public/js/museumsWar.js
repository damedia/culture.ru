var WarArchive = {
    imagesArray: [],
    zoomIcon:'',
    init: function(icon){
        $(".archive-table tr").click(function(e){
            WarArchive.zoomIcon = icon;
            var index = $(".archive-table tr").index($(this));
            WarArchive.loadImages(index);
        });
    },
    /**
     * Получить список изображений для папки под номером @num
     */
    loadImages: function(num){
        WarArchive.imagesArray[num] = [];
        
        var imagesNames = ['00006.jpg', '00007.jpg', '00008.jpg', '00009.jpg'];
        
        for (var j = 0; j < imagesNames.length; j++) {
            WarArchive.imagesArray[num].push({href:'/img/war_archive/'+num+'/'+imagesNames[j]});
        }
        console.log(num);
        WarArchive.fancyZoom(num);
    },
    /**
     * Инициализация FancyBox c зумом
     */
    fancyZoom: function(num) {
        $.fancybox.open(WarArchive.imagesArray[num], {
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