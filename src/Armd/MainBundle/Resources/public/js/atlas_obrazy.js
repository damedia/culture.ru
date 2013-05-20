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
            lang: AT.params.locale.toUpperCase(),
            noWheelZoom: 1
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
            if (json.success) {
                var objects = json.result;
                AT.objects = [];
            } else {
            }
            if (objects && objects.length) {
                for (i in objects) {
                    var oid = objects[i].id;
                    AT.objects[oid] = objects[i];
                    AT.objects[oid].prev = objects[parseInt(i)-1] != null ? objects[parseInt(i)-1].id : objects[objects.length-1].id;
                    AT.objects[oid].next = objects[parseInt(i)+1] != null ? objects[parseInt(i)+1].id : objects[0].id;
                    AT.placePoint(objects[i]);
                }
                AT.showPoint(objects[Math.floor(Math.random() * objects.length)].id);
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
                    width: 17
                }
            });
        $(point.container)
            .data('uid', object.id)
            .css({
                'margin-left': '-4px',
                'margin-top': '-12px'
            })
            .attr('title', object.title);

        AT.map.geometry.add(point);

        var eventT = PGmap.EventFactory.eventsType;
        eventT.mouseover = 'mouseover';
        eventT.mouseout = 'mouseout';
        // наведение на точку
        PGmap.Events.addHandler(point.container, eventT.mouseover, function(e) {
            $('img', point.container).css({width:50});
            $(point.container).css({marginLeft:-18, marginTop:-24});
            AT.showPoint($(point.container).data('uid'));
        });
        PGmap.Events.addHandler(point.container, eventT.mouseout, function(e) {
            $('img', point.container).css({width:17});
            $(point.container).css({marginLeft:-4, marginTop:-12});
        });
        // клик по точке
        PGmap.Events.addHandler(point.container, eventT.click, function(e) {
            AT.showPoint($(point.container).data('uid'));
        });
        return point;
    }
};

AT.showPoint = function(uid) {
    if(AT.currentPoint !== uid) {
        AT.currentPoint = uid;
        $('#map-scrollblock').html(AT.objects[uid].sideDetails);
        /*
        $.ajax({
            url: fetchSideDetailUri,
            data: { id: uid },
            success: function(res) {
                $('#map-scrollblock').html(res);
            }
        });
        */
    }
};

AT.nextPoint = function() {
    AT.showPoint(AT.objects[AT.currentPoint].next);
    return false;
};
AT.prevPoint = function() {
    AT.showPoint(AT.objects[AT.currentPoint].prev);
    return false;
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