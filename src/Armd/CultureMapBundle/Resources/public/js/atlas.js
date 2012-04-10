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

    // Добавляет макет на страницу
    this.onAddToParent = function (parentNode) {
        YMaps.jQuery(parentNode).append(this.element);
    };

    // Удаляет макет со страницы
    this.onRemoveFromParent = function () {
        this.element.remove();
    };

    // Устанавливает содержимое балуна
    this.setContent = function (content) {
        content.onAddToParent(this.content[0]);
    };

    // Обновляет балун
    this.update = function() {
        this.element.css("margin-top", "-" + (this.content.height() + 30 ) + "px");
    };
};

function balloonReadMore(id) {
    $('#subject-details').load('/ru/sys/map/subject?id='+id, function(){
        $.scrollTo('#anchor-details', 1000, { easing:'easeOutQuad' });
    });
    return false;
}

function hideBullshit(map) {
    var stylebg = new YMaps.Style();
    stylebg.polygonStyle = new YMaps.PolygonStyle();
    stylebg.polygonStyle.fill = true;
    stylebg.polygonStyle.outline = false;
    stylebg.polygonStyle.strokeWidth = 0;
    stylebg.polygonStyle.strokeColor = "ffffff";
    stylebg.polygonStyle.fillColor = "ffffff";
    var gbgOptions = {
        hasBalloon: false,
        hasHint: false
    };
    var gbg= new YMaps.Polygon([new YMaps.GeoPoint(180,90),new YMaps.GeoPoint(180,-90),new YMaps.GeoPoint(0,-90),new YMaps.GeoPoint(0,90)]);
    var gbg2= new YMaps.Polygon([new YMaps.GeoPoint(180,90),new YMaps.GeoPoint(180,-90),new YMaps.GeoPoint(-100,-90),new YMaps.GeoPoint(-100,90)]);
    var gbg3= new YMaps.Polygon([new YMaps.GeoPoint(0,90),new YMaps.GeoPoint(0,-90),new YMaps.GeoPoint(-100,-90),new YMaps.GeoPoint(-100,90)]);
    map.addOverlay(gbg);
    gbg.setOptions(gbgOptions);
    gbg.setStyle(stylebg);
    map.addOverlay(gbg2);
    gbg2.setOptions(gbgOptions);
    gbg2.setStyle(stylebg);
    map.addOverlay(gbg3);
    gbg3.setOptions(gbgOptions);
    gbg3.setStyle(stylebg);
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
    map.enableScrollZoom();

    //hideBullshit(map);

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
                var markers = [];

                var s = new YMaps.Style();
                s.iconStyle = new YMaps.IconStyle();
                s.iconStyle.offset = new YMaps.Point(-12, -12);
                s.iconStyle.href = defaultMarkerUrl;
                s.iconStyle.size = new YMaps.Point(24, 24);
                s.balloonStyle = { template: sampleBalloonTemplate };

                // отрисовка булавок
                for (var i=0; i<json.result.length; i++) {
                    var row = json.result[i];
                    var placemark = new YMaps.Placemark(new YMaps.GeoPoint(row.lng, row.lat));
                    placemark.setStyle(s);
                    placemark.id = row.id;
                    placemark.name = row.title;
                    placemark.setBalloonContent(row.title);

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
