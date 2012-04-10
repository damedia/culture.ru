// Шаблон балуна
function SampleBalloonLayout() {
    this.element = YMaps.jQuery('<div class="b-simple-balloon-layout map-point-opened"> \
                                    <div class="map-point-marker"></div> \
                                    <div class="map-point-hide"></div> \
                                    <div class="content"></div> \
                                 </div>');

    this.close = this.element.find(".map-point-hide");
    this.content = this.element.find(".content");

    // Отключает кнопку закрытия балуна
    this.disableClose = function(){
        this.close.unbind("click").css("display", "none");
    };

    // Включает кнопку закрытия балуна
    this.enableClose = function(callback){
        this.close.bind("click", callback).css("display", "");
        return false;
    };

    // Добавляет макет на страницу  <div class="YMaps-balloon" style="position: absolute; z-index:200;">
    this.onAddToParent = function (parentNode) {
        YMaps.jQuery(parentNode).append(this.element);
        this.update();
    };

    // Удаляет макет со страницы
    this.onRemoveFromParent = function () {
        this.element.remove();
        
    };

   
    // Устанавливает содержимое балуна
    this.setContent = function (content) {
        YMaps.jQuery(content).find('.map-point-wrapper').remove();
        content.onAddToParent(this.content[0]);
        
    };

    // Обновляет балун
    this.update = function() {
        //this.element.css("margin-top", "-" + (this.content.height() - 14 ) + "px");
        //this.element.css("margin-top", "-" + (thi ) + "px");

    };
};

function balloonReadMore(id) {
    $('#subject-details').load('/ru/sys/map/subject?id='+id, function(){
        $.scrollTo('#anchor-details', 1000, { easing:'easeOutQuad' });
    });
    return false;
}

///////////////////////////////////////////////////////////////
YMaps.jQuery(function(){

    var map = new YMaps.Map(YMaps.jQuery('#YMapsID')[0]);
    var initLat = 99.933497,
        initLng = 65.948073,
        iniZoom = 3;
    map.setType(YMaps.MapType.SATELLITE);
    map.setCenter(new YMaps.GeoPoint(initLat, initLng), iniZoom);

    map.addControl(new YMaps.TypeControl());
    map.addControl(new YMaps.ToolBar());
    map.addControl(new YMaps.Zoom());
    //map.addControl(new YMaps.MiniMap());
    map.addControl(new YMaps.ScaleLine());
    //map.enableScrollZoom();

    // Задает стиль для коллекции регионов
    var sampleBalloonTemplate = new YMaps.LayoutTemplate(SampleBalloonLayout);
    var regionDefaultStyle = {
        polygonStyle: { fillColor: "ff000010", strokeColor: "FFCB00a0" }, // стиль контура регионов
        balloonStyle: { template: sampleBalloonTemplate },
        hasHint: true
    };
    var regionHoverStyle = {
        polygonStyle: { fillColor: "f0f0f030", strokeColor: "ff0000" }, // стиль контура регионов при наведении курсора мыши
        balloonStyle: { template: sampleBalloonTemplate },
        hasHint: true
    };

    // Загрузка меток объектов
    $.ajax({
        url: '/ru/sys/map/markers',
        data: { },
        success: function(json) {
            if (json.success) {
                var cluster = new PlacemarkClusterer(map, null, {
                    gridSize: 30, // размер ячейки кластера
                    maxZoom: 16, // уровень зума, после которого показываются все маркеры
                    style: {
                        url: groupMarkerUrl, // url иконки группы маркеров кластера. Определяется в шаблоне атласа
                        width:  53,  // ширина
                        height: 52 // высота
                    }
                });

                var markersStyles = [];
                for (var i=1; i<=markersIcons.length; i++) {
                    var s = new YMaps.Style();
                    s.iconStyle = new YMaps.IconStyle();
                    s.iconStyle.offset = new YMaps.Point(-13, -21);
                    s.iconStyle.href = markersUrl + markersIcons[i];
                    s.iconStyle.size = new YMaps.Point(27, 43);
                    s.balloonStyle = { template: sampleBalloonTemplate };
                    markersStyles[i] = s;
                }

                // отрисовка булавок
                var markers = [];
                for (var i=0; i<json.result.length; i++) {
                    var row = json.result[i];
                    var placemark = new YMaps.Placemark(new YMaps.GeoPoint(row.lng, row.lat));
                    placemark.setStyle(markersStyles[row.icon]);
                    placemark.id = row.id;
                    //placemark.name = row.title;
                    //placemark.setBalloonContent(row.title);

                    // клик по булавке
                    YMaps.Events.observe(placemark, placemark.Events.Click, function(p, e){
                        $.ajax({
                            url: '/ru/sys/map/object',
                            data: { id: p.id },
                            success: function(response) {
                                p.setBalloonContent(response);
                                p.openBalloon();
                            }
                        });
                    });

                    markers.push(placemark);
                }
                cluster.addPlacemarks(markers);
            }
        }
    });

    // Загрузка регионов
    YMaps.Regions.load("ru", function(state, response) {
        if (state != YMaps.State.SUCCESS) {
            alert("Во время выполнения запроса произошла ошибка: " + response.error.message);
            return false;
        }

        response.forEach(function(v){
            var title = v.name;
            var g = v.getGeometry();

            for (var i=0; i<g.length; i++) {
                var item = g[i];
                var placemark = new YMaps.Polygon(item.coords);
                placemark.setOptions({ hasBalloon: false });
                placemark.setStyle(regionDefaultStyle);
                placemark.setHintContent(v.name);

                // клик по региону
                YMaps.Events.observe(placemark, placemark.Events.Click, function(p, e){
                    var hintContent = p.getHintContent();
                    //map.setBounds(new YMaps.GeoCollectionBounds(p.getPoints())); // fit map to region
                    $.ajax({
                        url: '/ru/sys/map/region',
                        data: { id: hintContent },
                        success: function(response) {
                            p.setBalloonContent(response);
                            p.openBalloon();
                        }
                    });
                });
                
                YMaps.Events.observe(placemark, placemark.Events.DblClick, function(p, e){
                    var hintContent = p.getHintContent();
                    map.setBounds(new YMaps.GeoCollectionBounds(p.getPoints())); // fit map to region
                   return false;
                });
                
                // MouseEnter на регионе
                YMaps.Events.observe(placemark, placemark.Events.MouseEnter, function(p, e) {
                    p.setStyle(regionHoverStyle);
                });

                // MouseLeave на регионе
                YMaps.Events.observe(placemark, placemark.Events.MouseLeave, function(p, e) {
                    p.setStyle(regionDefaultStyle);
                });

                map.addOverlay(placemark);

            }

        });

    });

});
