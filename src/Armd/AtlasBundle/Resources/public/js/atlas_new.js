/**
 * Атлас. Новый
 */
var AT = {};

AT.version = '0.2';
AT.map = null;

AT.init = function(params) {
    console.info('init');

    AT.initMap(params);
    AT.initGeocoder();
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
};

AT.initGeocoder = function() {
    var geocoder = $('#geocoder'),
        resultBox = geocoder.find('.result-box'),
        searchInput = geocoder.find('.search'),
        searchSubmit = geocoder.find('.submit'),
        searchTerm = searchInput.val();

    searchInput.autocomplete({
        source: function(request, response) {
            console.log(request, response);
        }
    });

    searchSubmit.click(function(){
        console.log('Try to search:', searchTerm);
        AT.map.search({
            q: searchTerm,
            type: 'search'
        }, function(r){
            var json = $.parseJSON(r);
            if (json.success) {
                console.log(json);
                for (el in json.res) {
                    console.log(el);
                }

            }

        });
    });
};


