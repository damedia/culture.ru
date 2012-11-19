/**
 * Атлас. Образы России
 */
var AT = {};

AT.version = '0.3';
AT.map = null;
AT.params = {};

AT.init = function(params) {
    this.params = params;

    AT.initMap(params);
    AT.initUI();
};

AT.initMap = function(params) {
    var localeTiles = {
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
    var map_el = document.getElementById(params.map),
        parameters = {
            roundRobin: {
                tiles: localeTiles[AT.params.locale]
            },
            coord: new PGmap.Coord(params.center[0], params.center[1], true),
            zoom: params.zoom,
            minZoom: 3,
            lang: AT.params.locale.toUpperCase()
        };

    this.map = new PGmap(map_el, parameters);
    this.map.controls.addControl('slider');

    this.map.layers.LEFTLIMITLON  = PGmap.Utils.mercX(params.leftLon);
    this.map.layers.RIGHTLIMITLON = PGmap.Utils.mercX(params.rightLon);

    this.map.balloon.content.parentNode.style.width = '300px';
};

AT.initUI = function() {
    $.ajax({
        url: fetchMarkersUri,
        data: { category: obrazCategoryId },
        dataType: 'json',
        success: function(json) {
            //console.log(json);
            if (json.success) {
                var objects = json.result;
            } else {
                //console.error(json.message);
            }
            if (objects && objects.length) {
                var points = [];
                for (i in objects) {
                    AT.placePoint(objects[i]);
                }
            }
        }
    });
};

AT.placePoint = function(object) {
    if (object.obraz && object.imageUrl!='') {
        var point = new PGmap.Point({
                coord: new PGmap.Coord(object.lon, object.lat, true),
                width: 24,
                height: 38,
                backpos: '0 0',
                innerImage: {
                    src: object.imageUrl,
                    width: 50
                }
            });
        $(point.container)
            .data('uid', object.id)
            .css({
                'margin-left': '-12px',
                'margin-top': '-38px'
            });
        AT.map.geometry.add(point);

        // клик по точке
        var eventT = PGmap.EventFactory.eventsType;
        PGmap.Events.addHandler(point.container, eventT.click, function(e) {
            var uid = $(point.container).data('uid');
            $.ajax({
                url: fetchMarkerDetailUri,
                data: { id: uid },
                success: function(res) {
                    point.name = res;
                    point.balloon = AT.map.balloon;
                    point.toggleBalloon();
                }
            });
        });
        return point;
    }
};

/*
new function () {
    var debug    = true;
    var original = window.console;
    window.console = {};
    ['log', 'dir'].forEach(function (method) {
        console[method] = function () {
            return (debug && original) ? original[method].apply(original, arguments);
        }
    });
};
*/