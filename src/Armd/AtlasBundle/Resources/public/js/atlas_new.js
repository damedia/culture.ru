/**
 * Атлас. Новый
 */
var AT = {};

AT.version = '0.3';
AT.map = null;
AT.filterTags = [];
AT.clusterPoints = null;
AT.regions = [];

AT.init = function(params) {
    console.info('Init Atlas');

    AT.initMap(params);
    AT.initGeocoder();
    AT.initUI();
    AT.initFilters();
    AT.initHacks();

    // Сабмитим форму
    $('#atlas-form').submit();
};

AT.initMap = function(params) {
    var map_el = document.getElementById(params.map),
        parameters = {
            roundRobin: {
                tiles: [
                    'http://h01.tiles.tmcrussia.com/map/', 'http://h02.tiles.tmcrussia.com/map/', 'http://h03.tiles.tmcrussia.com/map/','http://h04.tiles.tmcrussia.com/map/',
                    'http://h05.tiles.tmcrussia.com/map/', 'http://h06.tiles.tmcrussia.com/map/', 'http://h07.tiles.tmcrussia.com/map/'
                ]
            },
            coord: new PGmap.Coord(params.center[0], params.center[1], true),
            zoom: params.zoom
        };

    this.map = new PGmap(map_el, parameters);
    this.map.balloon.content.parentNode.style.width = '400px';
    this.map.controls.addControl('slider');
};

AT.initGeocoder = function() {
    var geocoder = $('.simple-search'),
        resultBox = geocoder.find('.result-box'),
        searchInput = $('#ss_input'),
        searchSubmit = geocoder.find('.submit'),
        searchTerm = searchInput.val();

    searchInput.val('');

    searchInput.autocomplete({
        source: function(request, response) {
            //console.log(request, response);
            //console.log('Try to search:', searchTerm);

            AT.map.search({
                q: request.term,
                type: 'search'
            }, function(r){
                var json = $.parseJSON(r);
                if (json.success) {
                    // console.log(json);
                    var results = [];

                    for (var i in json.res) {

                        var el = json.res[i];

                        // el - найденный элемент (addr|poi)
                        if (el.type == "addr") {
                            for (var j in el.matches) {
                                var match = el.matches[j];

                                var obj = {
                                    label: match.addr,
                                    value: match.addr,
                                    match: match
                                };

                                results.push(obj);
                            }
                        }
                    }

                    response(results);
                }

            });
        },
        select: function (event, ui) {
            //console.log(ui.item.match);
            var lon = ui.item.match.x,
                lat = ui.item.match.y,
                zoom = ui.item.match.zoom;
            AT.map.setCenter(new PGmap.Coord(lon, lat, true), zoom);
        }
    }).data('autocomplete')._renderItem = function(ul, item) {
        return $("<li></li>")
            .data("item.autocomplete", item)
            .append('<a>' + item.label + '</a>')
            .appendTo(ul);
    }

};

AT.initUI = function() {
    $('#atlas-filter-form').ajaxForm({
        beforeSubmit: function(){
            //console.log('xxx');
            $('#ajax-loading').show();
        },
        success: function(responseText, statusText, xhr, $form){
            $('#ajax-loading').hide();
            var json = $.parseJSON(responseText);
            if (json.success) {
                var objects = json.result;
            } else {
                console.error(json.message);
            }

            AT.clearMap();

            if (objects && objects.length) {
                //var minLon=1000, maxLon=0, minLat=1000, maxLat=0;
                var points = [];
                for (i in objects) {
                    points.push(AT.placePoint(objects[i]));
                }

                // clusterize points
                AT.clusterPoints = new PGmap.GeometryLayer({
                    points: points,
                    clusterSize: 100,
                    clusterImage: bundleImagesUri + "/klaster_1.1.png"
                });
                AT.clusterPoints.setClusterImageByCount = function(count) {
                    return "0px 0px";
                };
                AT.clusterPoints.setClusters.callback = function() {
                    for (var n = AT.clusterPoints.clusters.length; n--; ) {
                        (function(cluster){
                            PGmap.Events.addHandlerByName(cluster.element, 'click', function(e){

                                console.log('click on cluster');

                                // Балун для кластера
                                cluster.balloon = AT.map.balloon;

                                var ids = [];
                                for (var i=0; i<cluster.points.length; i++) {
                                    ids.push($(cluster.points[i].container).data('uid'));
                                }
                                // Содержимое балуна
                                //cluster.name = ids.join(',');
                                console.log('cluster points ids:', cluster.name);

                                // Если достигнут последний уровень зума, показываем бабл
                                if ((AT.map.globals.maxZoom() - AT.map.globals.getZoom()) == 1) {
                                    if (! cluster.balloon.element.offsetHeight || cluster.balloon.currEl != cluster ) {

                                        $.ajax({
                                            url: fetchClusterDetailUri,
                                            data: { ids: ids },
                                            success: function(res){
                                                console.log(res);
                                                cluster.name = res;
                                                cluster.balloon.open(cluster);
                                                //cont.style.zIndex = '75';
                                            }
                                        });

                                    } else {
                                        cluster.balloon.close();
                                        //cont.style.zIndex = '73';
                                    }
                                } else {
                                    AT.clusterPoints.globals.mapObject().setCenterByBbox(cluster.bbox);
                                }
                            }, 'click_' + cluster.index);
                            PGmap.Events.addHandlerByName(cluster.element, 'mouseover', function(e){
                                //this.style.backgroundPosition = ( cluster.count <= 10 ) ? "-120px -69px" : ( cluster.count < 100  ) ? "-56px -69px" : "5px -69px";
                            }, 'mouseover_' + cluster.index);
                            PGmap.Events.addHandlerByName(cluster.element, 'mouseout', function(e){
                                //this.style.backgroundPosition = ( cluster.count <= 10 ) ? "-120px 0" : ( cluster.count < 100  ) ? "-56px 0" : "5px 0";
                            }, 'mouseout_' + cluster.index);
                        })(AT.clusterPoints.clusters[n]);
                    }
                };
                AT.clusterPoints.setClusters();
            }
        }
    });

    // Инициализируем список регионов
    var regionsJsonPath = bundleImagesUri + '/../js/regions.json',
        regionsSelector = $('#regions-selector select');
    $.getJSON(regionsJsonPath, function(res){
        AT.regions = res;
        for (var i=0; i<res.length; i++) {
            regionsSelector.append('<option value="'+res[i].name+'">'+res[i].name+'</option>');
        }
        // При выборе региона, зумим карту к нему
        regionsSelector.chosen({ no_results_text:"Не найдено" }).change(function(){
            AT.map.search({
                q: $(this).val(),
                type: 'search'
            }, function(r){
                var json = $.parseJSON(r);
                if (json.success) {
                    var bbox = json.res[0].bbox;
                    var addrBbox = {
                        lon1: PGmap.Utils.mercX(bbox.x1),
                        lon2: PGmap.Utils.mercX(bbox.x2),
                        lat1: PGmap.Utils.mercY(bbox.y1),
                        lat2: PGmap.Utils.mercY(bbox.y2)
                    };
                    AT.map.setCenterByBbox(addrBbox);
                }
            });
        });
    });

};

// Init filters
AT.initFilters = function(){
    $('.atlas-filter-form').find('.simple-filter-options > label > span').click(function(e){
        // перехват обработчика из function.js
        e.preventDefault();
        e.stopPropagation();
        $(this).closest('label').toggleClass('checked');
        // сбор отмеченных тегов
        AT.submitFiltersForm();
    });

    $('.atlas-filter-form').find('.check_all').click(function(e){
        // сбор отмеченных тегов
        AT.submitFiltersForm();
    });
};

AT.clearMap = function() {

    console.info('AT.clearMap');

    var points = AT.map.geometry.get({ type:'points' });

    if (points.length > 0) {
        for (var i=points.length; i--; ) {
            AT.map.geometry.remove(points[i]);
        }

        for (var n = AT.clusterPoints.clusters.length; n--; ) {
            (function(cluster){
                PGmap.Events.removeHandlerByName(cluster.element, 'click',  'click_' + cluster.index);
                PGmap.Events.removeHandlerByName(cluster.element, 'mouseover', 'mouseover_' + cluster.index);
                PGmap.Events.removeHandlerByName(cluster.element, 'mouseout',  'mouseout_' + cluster.index);
            })(AT.clusterPoints.clusters[n]);
        }

        AT.clusterPoints.removeClusters();
    }
};

AT.placePoint = function(object) {

    if (object.obraz) {

        //console.log( object );

        var point = new PGmap.Point({
                coord: new PGmap.Coord(object.lon, object.lat, true),
                width: 24,
                height: 38,
                backpos: '0 0',
                innerImage: {
                    src: object.imageUrl,
                    width: 32
                }
            });
    } else {
        var point = new PGmap.Point({
                coord: new PGmap.Coord(object.lon, object.lat, true),
                width: 24,
                height: 38,
                backpos: '0 0',
                url: object.icon
            });
    }
    //console.log( $(point.element) );
    $(point.container)
        .data('uid', object.id)
        .css({
            'margin-left': '-12px',
            'margin-top': '-38px'
        });
    AT.map.geometry.add(point);

    // клик по точке
    PGmap.Events.addHandler(point.container, 'click', function(e) {
        var uid = $(point.container).data('uid');
        $.ajax({
            url: fetchMarkerDetailUri,
            data: { id: uid },
            success: function(res) {

                console.log('click on point');

                //point.addContent(res);
                point.name = res;
                point.balloon = AT.map.balloon;
                //PGmap.Events.removeHandler(point.container, 'click', point.toggleBalloon);
                point.toggleBalloon();
            }
        });
    });

    return point;
};

AT.initHacks = function() {
    $('.g-closer').live('click', function(){
        console.log('try to close bubble');
        $(this).closest('.b-balloon').data('point').hideBalloon();
        return false;
    });
};

AT.collectTagsValue = function() {
    var filterTags = [];
    $('.atlas-filter-form').find('.simple-filter-options > label.checked > span').each(function(i,el){
        filterTags.push( $(this).data('tag') );
    });
    $('#category-id').val(filterTags);
    return filterTags;
};

AT.submitFiltersForm = function() {
    console.info('AT.submitFiltersForm');
    // Собираем отмеченные категории
    AT.collectTagsValue();
    // Сабмитим форму
    $('#atlas-filter-form').submit();
};
