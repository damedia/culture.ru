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
            coord: new PGmap.Coord(4212745.219334374, 7489549.153770343),
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

    var customPoint = new PGmap.Point({
            coord: new PGmap.Coord(4187521.87177, 7473956.8430),
            url: 'http://mkprom.dev.armd.ru/bundles/armdculturemap/images/pin_science.png'
        }),
        customBalloon = new PGmap.Balloon({
            content: '<h1>Head1</h1><p>bla-bla-bla</p>',
            isClosing: true,
            isHidden: false
        });
    /*
        customPolyline = new PGmap.Polyline({
            points:	[
                new PGmap.Coord(37.418824, 55.712948, true),
                new PGmap.Coord(37.438824, 55.702948, true),
                new PGmap.Coord(37.438824, 55.752948, true)
            ],
            style: { color:"#f3f", lineHeight:5 }
        });
        */

    map.geometry.add(customPoint);
    customPoint.addBalloon(customBalloon);

    //map.geometry.add(customPolyline);

    var customPolygon = new PGmap.Polygon({
        points: [
            new PGmap.Coord(4187445, 7461492)
            , new PGmap.Coord(4208877, 7468028)
        ]
        , draggable: 1
        , style: {color:"#f00", lineHeight: 5, backgroundColor: "#fde", lineOpacity:0.5, backgroundOpacity: 0.5 }
    });
    map.geometry.add(customPolygon);
    customPolygon.graphic.attr({"stroke":'#f00'});

});
