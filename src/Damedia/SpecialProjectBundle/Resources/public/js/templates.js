$(document).ready(function(){
	$('.fancybox').fancybox({
        prevEffect: 'none',
        nextEffect: 'none'
    });

    $(".zoom-fancybox").click(function() {
        var $img   = $("img", $(this).attr("href")).clone(),
            $title = $("div", $(this).attr("href"));

        $("a", $title).css("color", "#fff");
        $img.css("visibility", "hidden");

        $.fancybox.open({
            autoSize:   false,
            scrolling:  "no",
            type:       "html",
            title:      $title.html(),
            content:    $img,
            helpers : {
                title: {
                    type: 'outside'
                }
            },
            afterShow: function() {
                $img.css("visibility", "visible");
                $img.z({
                    width: $.fancybox.inner.width(),
                    height: $.fancybox.inner.height(),
                    initial_POSITION: "0, 0",
                    initial_ZOOM: $.fancybox.inner.width() / $img.width() * 100,
                    button_ICON_IMAGE: '/vendor/zp/zoom_assets/icons.png',
                    border_SIZE: 0,
                });
            },
            onUpdate: function() {
                var $fbInner = $($.fancybox.inner),
                    fbInnerWidth  = $fbInner.width(),
                    fbInnerHeight = $fbInner.height();

                zoomer_.sW = fbInnerWidth;
                zoomer_.sH = fbInnerHeight;
                zoomer_.zoom = $.fancybox.inner.width() / $img.width() * 100
                zoomer_.$holder.width(fbInnerWidth);
                zoomer_.$holder.height(fbInnerHeight);

                zoomer_.Reset();
            }
        });
    });
});