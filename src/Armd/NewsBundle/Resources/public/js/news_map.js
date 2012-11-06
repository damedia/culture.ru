/**
 * Атлас новостей.
 */
var AT = {};

AT.version = '0.4';
AT.params = {};
AT.map = null;
AT.regions = [];
AT.localeTiles = {
    'en': [
        'http://h01.tiles.tmcrussia.com/map_en/', 'http://h02.tiles.tmcrussia.com/map_en/',
        'http://h03.tiles.tmcrussia.com/map_en/', 'http://h04.tiles.tmcrussia.com/map_en/',
        'http://h05.tiles.tmcrussia.com/map_en/', 'http://h06.tiles.tmcrussia.com/map_en/',
        'http://h07.tiles.tmcrussia.com/map_en/'
    ],
    'ru': [
        'http://h01.tiles.tmcrussia.com/map/', 'http://h02.tiles.tmcrussia.com/map/', 'http://h03.tiles.tmcrussia.com/map/','http://h04.tiles.tmcrussia.com/map/',
        'http://h05.tiles.tmcrussia.com/map/', 'http://h06.tiles.tmcrussia.com/map/', 'http://h07.tiles.tmcrussia.com/map/'
    ]
};

//------------------------------------------
// Constructor
AT.init = function(params) {
    this.params = params;
    AT.initFilter();
    AT.initMap(params);
    AT.initGeocoder();
};

//------------------------------------------
// Init Map
AT.initMap = function(params) {
    var map_el = document.getElementById(params.map);
    var parameters = {
        roundRobin: { tiles: AT.localeTiles[AT.params.locale] },
        coord: new PGmap.Coord(params.center[0], params.center[1], true),
        zoom: params.zoom,
        minZoom: 3,
        lang: AT.params.locale.toUpperCase()
    };

    this.map = new PGmap(map_el, parameters);
    this.map.controls.addControl('slider');

    this.map.layers.LEFTLIMITLON = 2081245.7489734937;
    this.map.layers.RIGHTLIMITLON = -19736433.1934625;

    this.map.balloon.content.parentNode.style.width = '300px';
};

//------------------------------------------
// Init Filter
AT.initFilter = function(){

    // Ajax Form init
    $('#news-map-filter').ajaxForm({
        dataType: 'json',
        beforeSubmit: function(){
            //console.log('beforeSubmit');
        },
        success: function(response, statusText, xhr, $form){
            if (response.success) {
                if (! response.result.data.length) {
                    alert('Not items');
                    return;
                }

                // Очищаем карту
                AT.clearMap();

                // Рисовать точки или фоточки
                var showAsImages = response.result.filter.show_images > 0 ? true : false;

                // Наносим точки
                for (var i in response.result.data) {

                    var point = response.result.data[i];

                    AT.placePoint(point, showAsImages, function(e){
                        var uid = $(e.currentTarget).data('uid')
                            point = $(e.currentTarget).data('point');

                        // Show balloon
                        $.ajax({
                            url: AT.params.fetchMarkerDetailUri,
                            data: { id: uid },
                            success: function(res){
                                point.name = res; // контент балуна
                                point.balloon = AT.map.balloon;
                                point.toggleBalloon();
                            }
                        })
                    });
                }
            } else {
                alert(response.message);
            }
        }
    });

    // Range Datepickers
    $("#date-from").datepicker({
        defaultDate: "+1w",
        changeMonth: false,
        numberOfMonths: 1,
        onClose: function(selectedDate) {
            $("#date-to").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#date-to").datepicker({
        defaultDate: "+1w",
        changeMonth: false,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
            $("#date-from").datepicker("option", "maxDate", selectedDate);
        }
    });

};

//------------------------------------------
AT.clearMap = function() {
    var points = AT.map.geometry.get({ type:'points' });
    if (points.length > 0) {
        for (var i=points.length; i--; ) {
            AT.map.geometry.remove(points[i]);
        }
        if (AT.clusterPoints) {
            for (var n = AT.clusterPoints.clusters.length; n--; ) {
                (function(cluster){
                    PGmap.Events.removeHandlerByName(cluster.element, 'click',  'click_' + cluster.index);
                    PGmap.Events.removeHandlerByName(cluster.element, 'mouseover', 'mouseover_' + cluster.index);
                    PGmap.Events.removeHandlerByName(cluster.element, 'mouseout',  'mouseout_' + cluster.index);
                })(AT.clusterPoints.clusters[n]);
            }
            AT.clusterPoints.removeClusters();
        }
    }
};

//------------------------------------------
AT.placePoint = function(object, showAsImages, onClick) {
    if (! (object.lon || object.lat))
        return false;

    var point;
    if (showAsImages) {
        point = new PGmap.Point({
            coord: new PGmap.Coord(object.lon, object.lat, true),
            width: 42,
            height: 39,
            backpos: '0 0',
            innerImage: {
                src: object.imageUrl,
                width: 50
            }
        });
    } else {
        point = new PGmap.Point({
            coord: new PGmap.Coord(object.lon, object.lat, true)
        });
    }
    $(point.container).data('uid', object.id)
                      .data('point', point)
                      .attr('title', object.title);
    AT.map.geometry.add(point);
    PGmap.Events.addHandler(point.container, PGmap.EventFactory.eventsType.click, onClick);
    return point;
};

//------------------------------------------
AT.initGeocoder = function() {
    var geocoder = $('.geocoder-search'),
        resultBox = geocoder.find('.result-box'),
        searchInput = $('#ss_input'),
        searchSubmit = geocoder.find('.submit'),
        searchTerm = searchInput.val();
    searchInput.val('');
    searchInput.autocomplete({
        source: function(request, response) {
            AT.map.search({
                q: request.term,
                type: 'search',
                lng: AT.params.locale
            }, function(r){
                var json = $.parseJSON(r);
                if (json.success) {
                    var results = [];
                    for (var i in json.res) {
                        var el = json.res[i];
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
