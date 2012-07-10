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

    $.ajax({
        url: 'http://local.armd.ru/app_dev.php/atlas/proxy?region=&search=&type%5B%5D=15&type%5B%5D=1&type%5B%5D=13&type%5B%5D=3&type%5B%5D=14&type%5B%5D=41&type%5B%5D=8&type%5B%5D=9&type%5B%5D=10&type%5B%5D=6&type%5B%5D=5&type%5B%5D=11&type%5B%5D=4&type%5B%5D=12&type%5B%5D=2&type%5B%5D=7',
        success: function(json){
            if (json.success) {
                $.each(json.result, function(i, el){
                    // place marker
                    var customPoint = new PGmap.Point({
                            coord: new PGmap.Coord(el.lng, el.lat, true),
                            url: 'http://mkprom.dev.armd.ru/bundles/armdculturemap/images/pin_science.png'
                        }),
                        customBalloon = new PGmap.Balloon({
                            content: el.text,
                            isClosing: true,
                            isHidden: true
                        });
                    customPoint.addBalloon(customBalloon);
                    map.geometry.add(customPoint);
                });
            }
        }
    });

    //------------------------
    $('#regions-selector').chosen();

});
