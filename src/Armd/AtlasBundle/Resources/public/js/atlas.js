var map;

$(function(){

    var map_el = document.getElementById('atlas-map'),
        parameters = {
            roundRobin: {
                tiles: [
                    'http://h01.tiles.tmcrussia.com/map/', 'http://h02.tiles.tmcrussia.com/map/', 'http://h03.tiles.tmcrussia.com/map/','http://h04.tiles.tmcrussia.com/map/',
                    'http://h05.tiles.tmcrussia.com/map/', 'http://h06.tiles.tmcrussia.com/map/', 'http://h07.tiles.tmcrussia.com/map/'
                ]
            },
            coord: new PGmap.Coord(37.648527, 55.723211, true),
            zoom: 10
        };
    map = new PGmap(map_el, parameters);

    /*
    map.controls.addControl('scale');
    map.controls.addControl('coords');
    map.controls.addControl('slider');
    map.controls.addControl('ruler');
    map.controls.addControl('route');
    */

    //------------------------
    // ajax-loading
    function showAjaxLoading() {
        $('#ajax-loading').show();
    }

    function hideAjaxLoading() {
        $('#ajax-loading').hide();
    }

    function clearAllPoints() {
        var objectsAll = map.geometry.get('all');
        $.each(objectsAll.points, function(i,el){
            map.geometry.remove(el);
        });
    }

    function drawPlacemarks(data) {

        var minLng=1000, maxLng=0,
            minLat=1000, maxLat=0;

        $.each(data, function(i, el){
            // place marker
            var customPoint = new PGmap.Point({
                    uid: el.id,
                    coord: new PGmap.Coord(el.lng, el.lat, true),
                    url: bundleImagesUri + '/pin' + el.type + '.png'
                }),
                customBalloon = new PGmap.Balloon({
                    content: el.text,
                    isClosing: true,
                    isHidden: true
                });
            customBalloon.setSize(350, 140);

            customPoint.addBalloon(customBalloon);

            $(customPoint.element).data('uid', el.id);

            PGmap.Events.addHandler(customPoint.element, 'click', function(e){
                var uid = $(customPoint.element).data('uid');

                $.ajax({
                    url: fetchMarkerDetailUri,
                    data: { id: uid },
                    success: function(res) {
                        $(customPoint.balloon.element).find('span').html($('<div class="w"></div>').append(res));
                        customPoint.balloon.show();
                    }
                });

            });

            map.geometry.add(customPoint);

            if (parseFloat(el.lat) > maxLat) maxLat = el.lat;
            if (parseFloat(el.lat) < minLat) minLat = el.lat;
            if (parseFloat(el.lng) > maxLng) maxLng = el.lng;
            if (parseFloat(el.lng) < minLng) minLng = el.lng;
        });

        var bbox = {
            lon1: PGmap.Utils.mercX(minLng),
            lon2: PGmap.Utils.mercX(maxLng),
            lat1: PGmap.Utils.mercY(minLat),
            lat2: PGmap.Utils.mercY(maxLat)
        };

        map.setCenterByBbox(bbox);
    }

    //-------------------------
    $('#regions-selector').chosen();

    // Скроллбокс для фильтра объектов
    $(window).load(function(){
        $('#map-types-pane').jScrollPane({
            showArrows: true,
            arrowScrollOnHover: true
        });
    });

    // Фильтр по типам объектов
    var cbTypes = $('#filter-types').find('.cb-type');
    cbTypes.on('change', function(){
        $('#filter-types').submit();
    });

    // Отметить/снять отметку со всех галок
    $('.atlas-aside-group-menu a').click(function(){
        if ($(this).hasClass('select')) {
            cbTypes.each(function(i,el){
                $(el).attr('checked', 'checked');
            });
        } else {
            cbTypes.each(function(i,el){
                $(el).removeAttr('checked');
            });
        }
        $('#filter-types').submit();
        return false;
    });

    // Filter form
    $('#filter-types').ajaxForm({
        url: fetchMarkersUri,
        beforeSubmit: function() {
            clearAllPoints();
            showAjaxLoading();
        },
        dataType: 'json',
        success: function(res) {
            hideAjaxLoading();
            if (res.result.length) {
                drawPlacemarks(res.result);
            } else {
                alert('Ничего не найдено.');
            }
        }
    });

    // load and place markers
    $('#filter-types').submit();

    // geocoder
    var geocoder = $('#geocoder'),
        geocoderSearch = geocoder.find('.search'),
        geocoderSubmit = geocoder.find('.submit'),
        geocoderResults = geocoder.find('.results');

    geocoderSubmit.click(function(){
        map.search({
            q: 'Новосибирск',
            type: 'search'
        }, function (res) {
            var r = $.parseJSON(res);
            if (r.success) {
                geocoderResults.empty();

                $.each(r.res, function(i,el){
                    console.log(el);
                    geocoderResults.append(el.subject_name);
                });
            }
        });
    });

    $('#route').click(function(){

        // проложить маршрут
        // http://route.tmcrussia.com/cgi/getroute?n=2&type=route,plan,indexes&method=optimal&p0x=54.99999999999999&p0y=37.99999998648516&p1x=54.99999999999999&p1y=38.99999998745852&e316fab34585f12b5506493c1c17d41c
        var points = [
            new PGmap.Coord(55,38,true),
            new PGmap.Coord(55,39,true)
        ];
        map.route.get({
            points: points
        });

        // нарисовать полилинию маршрута
        var routeObj = {"error":0,"suberror":0,"distance":32031,"time":2417,"count":141,"points":[[37.379780,55.763800],[37.379624,55.763810],[37.376182,55.762630],[37.375534,55.761334],[37.375776,55.760820],[37.375278,55.759750],[37.375024,55.759362],[37.374114,55.758422],[37.372132,55.755958],[37.371262,55.755860],[37.370272,55.756034],[37.369650,55.756358],[37.369432,55.756734],[37.369296,55.757736],[37.369412,55.763396],[37.369948,55.782016],[37.370116,55.783716],[37.370362,55.784872],[37.370748,55.786040],[37.371450,55.786872],[37.371906,55.787242],[37.372576,55.787610],[37.375120,55.788714],[37.375730,55.788824],[37.377024,55.788796],[37.386240,55.787160],[37.388212,55.786682],[37.388898,55.786376],[37.390628,55.785220],[37.392094,55.783986],[37.392916,55.783072],[37.394290,55.780670],[37.394752,55.780064],[37.398692,55.776752],[37.400168,55.775698],[37.400892,55.775274],[37.404352,55.773592],[37.406400,55.772792],[37.408850,55.772244],[37.411644,55.772056],[37.429970,55.772596],[37.434486,55.772810],[37.435746,55.773008],[37.437494,55.773464],[37.438446,55.773802],[37.446980,55.777968],[37.448810,55.778480],[37.451330,55.778836],[37.453024,55.778852],[37.455272,55.778642],[37.460402,55.777796],[37.464756,55.776874],[37.469480,55.776198],[37.470988,55.775720],[37.475410,55.775076],[37.476060,55.775058],[37.476740,55.775154],[37.477572,55.775536],[37.477942,55.775876],[37.479156,55.778098],[37.479434,55.779326],[37.481054,55.782812],[37.463932,55.785542],[37.465520,55.788702],[37.465702,55.789356],[37.466312,55.790242],[37.465150,55.790542],[37.459692,55.791265],[37.454476,55.791828],[37.454520,55.794334],[37.454446,55.796048],[37.465874,55.796102],[37.465922,55.797606],[37.470906,55.800232],[37.471324,55.800840],[37.475482,55.801930],[37.476506,55.801980],[37.476844,55.802222],[37.476876,55.802612],[37.476650,55.804856],[37.475704,55.812562],[37.475850,55.812802],[37.492584,55.809428],[37.491710,55.808860],[37.491316,55.808800],[37.490936,55.808916],[37.490174,55.809796],[37.489972,55.810218],[37.490040,55.810606],[37.490938,55.811586],[37.491520,55.812052],[37.495770,55.813610],[37.498632,55.814012],[37.500582,55.814382],[37.502116,55.814426],[37.502362,55.814284],[37.503574,55.812686],[37.503490,55.812600],[37.503206,55.812582],[37.486662,55.832772],[37.487582,55.832986],[37.487904,55.832600],[37.488415,55.832718],[37.489628,55.832996],[37.489610,55.833142],[37.489414,55.833410],[37.491124,55.833742],[37.491554,55.833308],[37.492034,55.832600],[37.495832,55.827772],[37.495938,55.827602],[37.496144,55.826638],[37.493366,55.824886],[37.492866,55.824668],[37.492660,55.824652],[37.492422,55.824768],[37.490908,55.826608],[37.490960,55.826774],[37.491310,55.826768],[37.506394,55.808340],[37.506794,55.807730],[37.506800,55.807532],[37.506490,55.807104],[37.506434,55.806584],[37.507794,55.806514],[37.512266,55.805368],[37.512642,55.805360],[37.512850,55.805500],[37.512782,55.805738],[37.512600,55.805854],[37.511502,55.806152],[37.512940,55.808010],[37.513962,55.809164],[37.534440,55.803760],[37.537002,55.802802],[37.536886,55.802678],[37.536476,55.801552],[37.535866,55.800618],[37.534666,55.799700],[37.531954,55.800836],[37.528840,55.801670]],"indexes":[0,66,101,140],"plan":[{"name":"","dist":475.792328,"dir":5,"from":[37.379786,55.763807],"to":[37.375776,55.760928]},{"name":"","dist":602.100018,"dir":5,"from":[37.375776,55.760928],"to":[37.372132,55.755958]},{"name":"ул. Черепковская 3-я","dist":331.899994,"dir":0,"from":[37.372132,55.755958],"to":[37.369296,55.757736]},{"name":"МКАД 59 км (внутр.)","dist":594.599976,"dir":0,"from":[37.369296,55.757736],"to":[37.369404,55.763076]},{"name":"МКАД 60 км (внутр.)","dist":908.400005,"dir":0,"from":[37.369404,55.763076],"to":[37.369672,55.771232]},{"name":"МКАД 61 км (внутр.)","dist":965.200012,"dir":0,"from":[37.369672,55.771232],"to":[37.369890,55.779902]},{"name":"МКАД 62 км (внутр.)","dist":698.700012,"dir":0,"from":[37.369890,55.779902],"to":[37.370792,55.786142]},{"name":"М9","dist":2121.500061,"dir":0,"from":[37.370792,55.786142],"to":[37.394752,55.780064]},{"name":"пр-кт Маршала Жукова","dist":5338.099976,"dir":4,"from":[37.394752,55.780064],"to":[37.469480,55.776198]},{"name":"","dist":480.399994,"dir":1,"from":[37.469480,55.776198],"to":[37.476740,55.775154]},{"name":"ул. Народного Ополчения","dist":910.600002,"dir":2,"from":[37.476740,55.775154],"to":[37.481054,55.782812]},{"name":"ул. Маршала Тухачевского","dist":1116.500013,"dir":5,"from":[37.481054,55.782812],"to":[37.463932,55.785542]},{"name":"ул. Генерала Глаголева","dist":545.399999,"dir":2,"from":[37.463932,55.785542],"to":[37.466312,55.790242]},{"name":"ул. Берзарина","dist":764.000025,"dir":5,"from":[37.466312,55.790242],"to":[37.454476,55.791828]},{"name":"ул. Живописная","dist":310.300003,"dir":4,"from":[37.454476,55.791828],"to":[37.454486,55.794614]},{"name":"","dist":159.799995,"dir":5,"from":[37.454486,55.794614],"to":[37.454446,55.796048]},{"name":"ул. Рогова","dist":716.900015,"dir":2,"from":[37.454446,55.796048],"to":[37.465874,55.796102]},{"name":"ул. Максимова","dist":170.900002,"dir":4,"from":[37.465874,55.796102],"to":[37.465948,55.797628]},{"name":"ул. Максимова","dist":806.400002,"dir":8,"from":[37.465948,55.797628],"to":[37.475792,55.801976]},{"name":"пл. Академика Курчатова","dist":128.099998,"dir":9,"from":[37.475792,55.801976],"to":[37.476858,55.802650]},{"name":"ул. Академика Курчатова","dist":1134.099993,"dir":5,"from":[37.476858,55.802650],"to":[37.475850,55.812802]},{"name":"ш. Волоколамское","dist":1114.500006,"dir":5,"from":[37.475850,55.812802],"to":[37.492584,55.809428]},{"name":"","dist":83.599998,"dir":5,"from":[37.492584,55.809428],"to":[37.491710,55.808860]},{"name":"ул. Панфилова","dist":198.199998,"dir":0,"from":[37.491710,55.808860],"to":[37.489998,55.810102]},{"name":"ул. Константина Царева","dist":1184.799998,"dir":0,"from":[37.489998,55.810102],"to":[37.503590,55.812730]},{"name":"ш. Ленинградское","dist":34.900002,"dir":5,"from":[37.503590,55.812730],"to":[37.503206,55.812582]},{"name":"ш. Ленинградское","dist":2474.699992,"dir":5,"from":[37.503206,55.812582],"to":[37.486662,55.832772]},{"name":"ул. Выборгская","dist":62.299999,"dir":5,"from":[37.486662,55.832772],"to":[37.487582,55.832986]},{"name":"Дворовый проезд","dist":47.299999,"dir":2,"from":[37.487582,55.832986],"to":[37.487904,55.832600]},{"name":"Дворовый проезд","dist":165.000004,"dir":5,"from":[37.487904,55.832600],"to":[37.489414,55.833410]},{"name":"ул. Выборгская","dist":113.900002,"dir":5,"from":[37.489414,55.833410],"to":[37.491124,55.833742]},{"name":"ул. Адмирала Макарова","dist":1415.499999,"dir":0,"from":[37.491124,55.833742],"to":[37.490908,55.826608]},{"name":"ш. Ленинградское","dist":42.599998,"dir":5,"from":[37.490908,55.826608],"to":[37.491310,55.826768]},{"name":"ш. Ленинградское","dist":2259.200016,"dir":4,"from":[37.491310,55.826768],"to":[37.506394,55.808340]},{"name":"","dist":146.099998,"dir":2,"from":[37.506394,55.808340],"to":[37.506490,55.807104]},{"name":"","dist":58.000000,"dir":2,"from":[37.506490,55.807104],"to":[37.506434,55.806584]},{"name":"","dist":394.500010,"dir":1,"from":[37.506434,55.806584],"to":[37.512266,55.805368]},{"name":"","dist":166.400002,"dir":5,"from":[37.512266,55.805368],"to":[37.511502,55.806152]},{"name":"ул. Балтийская","dist":369.200010,"dir":5,"from":[37.511502,55.806152],"to":[37.513962,55.809164]},{"name":"ул. Усиевича","dist":1418.100013,"dir":0,"from":[37.513962,55.809164],"to":[37.534440,55.803760]},{"name":"ул. Красноармейская","dist":192.799999,"dir":5,"from":[37.534440,55.803760],"to":[37.537002,55.802802]},{"name":"ул. Аэропортовская 1-я","dist":381.699991,"dir":5,"from":[37.537002,55.802802],"to":[37.534666,55.799700]},{"name":"","dist":427.651158,"dir":0,"from":[37.534666,55.799700],"to":[37.528844,55.801675]}]};
        //console.log(routeObj.points);

        var routePoints = [];
        for (var i=0; i<routeObj.points.length; i++) {
            routePoints.push(new PGmap.Coord(routeObj.points[i][0], routeObj.points[i][1], true));
        }

        var routeLine = new PGmap.Polyline({
            points:	routePoints,
            style: { color:"#f3f", lineHeight:5 }
        });
        PGmap.Events.addHandler(routeLine.element, 'mouseover', function(e){
            routeLine.element.setAttribute('stroke', '#f00');
        });
        PGmap.Events.addHandler(routeLine.element, 'mouseleave', function(e){
            routeLine.element.setAttribute('stroke', '#f80');
        });
        map.geometry.add(routeLine);

    });

});
