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
                //console.log(response.result.data);

                // Очищаем карту
                AT.clearMap();

                // Наносим точки
                for (var i in response.result.data) {
                    var point = response.result.data[i];
                    AT.placePoint(point, function(e){
                        var uid = $(e.target).data('uid')
                            point = $(e.target).data('point');

                        //console.log('click', uid, e);

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
                        //console.log('ajax:', AT.params.fetchMarkerDetailUri);
                    });
                }
            }
        }
    });

    // Range Datepickers
    $("#date-from").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function(selectedDate) {
            $("#date-to").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#date-to").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
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
AT.placePoint = function(object, onClick) {
    if (! (object.lon || object.lat))
        return false;
    var point = new PGmap.Point({
        coord: new PGmap.Coord(object.lon, object.lat, true)
    });
    $(point.container).data('uid', object.id)
                      .data('point', point)
                      .attr('title', object.title);
    AT.map.geometry.add(point);
    PGmap.Events.addHandler(point.container, PGmap.EventFactory.eventsType.click, onClick);
    return point;
};
