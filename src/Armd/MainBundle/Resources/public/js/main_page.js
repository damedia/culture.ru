var fetchMarkersUri = Routing.generate('armd_atlas_default_filter');
        fetchMarkerDetailUri = Routing.generate('armd_atlas_default_objectballoon'),
        fetchClusterDetailUri = Routing.generate('armd_atlas_default_clusterballoon'),
        bundleImagesUri = armdMk.asset('bundles/armdatlas/images'),
        obrazCategoryId = 74;

var armdMainPage = {
    init:function() {
        armdMainPage.initRandomRussiaImages();
        armdMainPage.initAtlas();
    },

    initAtlas: function() {
        AT.init({
            map:'map',
            center: [45,56],
            zoom: 4,
            leftLon: 10,
            rightLon: -190,
            locale: armdMk.locale
        });

    },

    initRandomRussiaImages: function() {
        $('#rusObrTab_tab .reload_btn').bind('click', function(event){
            event.preventDefault();
            armdMainPage.loadRandomRussiaImages();
        })

    },

    loadRandomRussiaImages:function() {
        $.ajax({
            url: Routing.generate('armd_main_random_russia_images'),
            cache: false,
            dataType:'html',
            type:'POST',
            data:{
                searchString:$('#search_text').val()
            },
            success:function (data) {
                $('#rusObrTab_tab .rusObr-block').html(data);
            }
        });
    },

    initScrollPane: function() {
        // This code was copied from index page and moved to function.
        // Don't sure it will work inside function but now it is not used.

        //scrollpane parts
        var scrollPane = $(".scroll-pane"),
                scrollContent = $(".scroll-content");

        scrollContent.width(scrollContent.width());

        //build slider
        var scrollbar = $(".scroll-bar").slider({
            slide:function (event, ui) {
                if (scrollContent.width() > scrollPane.width()) {
                    scrollContent.css("margin-left", Math.round(
                            ui.value / 100 * ( scrollPane.width() - scrollContent.width() )
                    ) + "px");
                }
                else {
                    scrollContent.css("margin-left", 0);
                }
            }
        });

        //append icon to handle
        var handleHelper = scrollbar.find(".ui-slider-handle")
                .mousedown(function () {
                    scrollbar.width(handleHelper.width());
                })
                .mouseup(function () {
                    scrollbar.width("100%");
                })
                .append("<span class='ui-icon ui-icon-grip-dotted-vertical'></span>")
                .wrap("<div class='ui-handle-helper-parent'></div>").parent();

        //change overflow to hidden now that slider handles the scrolling
        scrollPane.css("overflow", "hidden");

        //size scrollbar and handle proportionally to scroll distance
        function sizeScrollbar() {
            var remainder = scrollContent.width() - scrollPane.width();
            var proportion = remainder / scrollContent.width();
            var handleSize = scrollPane.width() - ( proportion * scrollPane.width() );
            scrollbar.find(".ui-slider-handle").css({
                width:handleSize,
                "margin-left":-handleSize / 2
            });
            handleHelper.width("").width(scrollbar.width() - handleSize);
        }

        //reset slider value based on scroll content position
        function resetValue() {
            var remainder = scrollPane.width() - scrollContent.width();
            var leftVal = scrollContent.css("margin-left") === "auto" ? 0 :
                    parseInt(scrollContent.css("margin-left"));
            var percentage = Math.round(leftVal / remainder * 100);
            scrollbar.slider("value", percentage);
        }

        //if the slider is 100% and window gets larger, reveal content
        function reflowContent() {
            var showing = scrollContent.width() + parseInt(scrollContent.css("margin-left"), 10);
            var gap = scrollPane.width() - showing;
            if (gap > 0) {
                scrollContent.css("margin-left", parseInt(scrollContent.css("margin-left"), 10) + gap);
            }
        }

        //change handle position on window resize
        $(window).resize(function () {
            resetValue();
            sizeScrollbar();
            reflowContent();
        });
        //init scrollbar size
        setTimeout(sizeScrollbar, 10);//safari wants a timeout
    }

};