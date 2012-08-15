/**
 * Атлас. Новый
 */
var AT = {};

AT.version = '0.2';
AT.map = null;

AT.init = function(params) {
    console.info('Init Atlas');

    AT.initMap(params);
    AT.initGeocoder();
    AT.initUI();
    AT.initHacks();

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

    $('#atlas-form').ajaxForm({
        success: function(responseText, statusText, xhr, $form){
            var objects = $.parseJSON(responseText);
            //console.log('ok', objects);

            AT.clearMap();

            if (objects && objects.length) {

                var minLon=1000, maxLon=0,
                    minLat=1000, maxLat=0;

                for (i in objects) {
                    var el = objects[i];

                    AT.placeObject(el);

                    /*
                    if (parseFloat(el.lat) > maxLat) maxLat = el.lat;
                    if (parseFloat(el.lat) < minLat) minLat = el.lat;
                    if (parseFloat(el.lon) > maxLon) maxLon = el.lon;
                    if (parseFloat(el.lon) < minLon) minLon = el.lon;
                    */
                }

                /*
                var bbox = {
                    lon1: PGmap.Utils.mercX(minLon),
                    lon2: PGmap.Utils.mercX(maxLon),
                    lat1: PGmap.Utils.mercY(minLat),
                    lat2: PGmap.Utils.mercY(maxLat)
                };

                AT.map.setCenterByBbox(bbox);
                */
            }
        }
    });

    var filterCheckboxes = $('#filter').find(':checkbox');

    // Принудительно грузим предварительно установленные в куке метки
    filterCheckboxes.removeAttr('checked');

    var filterCategories = $.cookie('filter_categories');

    if (! filterCategories) {
        // По-умолчанию, взводим чекбоксы с музеями
        $(filterCheckboxes[0]).attr('checked', 'checked');
        $("#filter .lvl-1:first .lvl-2:first :checkbox").each(function(i,el){
            $(el).attr('checked', 'checked');
        });
    } else {
        // Взводим чекбоксы из куков
        filterCategories = filterCategories.split(',');
        for (var i=0; i<filterCheckboxes.length; i++) {
            var categoryId = $(filterCheckboxes[i]).data('category');

            if ($.inArray(categoryId.toString(), filterCategories) >= 0) {
                $(filterCheckboxes[i]).attr('checked', 'checked');
            } else {
                $(filterCheckboxes[i]).removeAttr('checked');
            }
           
        }
    }

    // Сабмитим форму
    $('#atlas-form').submit();

    // При клике по чекбоксу сабмитим форму
    filterCheckboxes.bind('change', function(){

        // Собираем id всех отмеченных категорий и сохраняем в comma-separated строке в куке
        var categories = [];
        for (var i=0; i<filterCheckboxes.length; i++) {
            if ($(filterCheckboxes[i]).is(':checked')) {
                var categoryId = $(filterCheckboxes[i]).data('category');
                categories.push(categoryId);
            }
        }
        var cookieValue = categories.join(',');
        $.cookie('filter_categories', cookieValue);

        // Сабмитим форму
        $('#atlas-form').submit();
    });

};

AT.clearMap = function() {
    var points = AT.map.geometry.get({ type:'points' });
    if (points.length > 0) {
        for (var i=points.length; i--; ) {
            AT.map.geometry.remove(points[i]);
        }
    }
};

AT.placeObject = function(object) {
    var point = new PGmap.Point({
            coord: new PGmap.Coord(object.lon, object.lat, true),
            url: object.icon
        });
    var balloon = new PGmap.Balloon({
            content: '',
            isClosing: true,
            isHidden: true
        });
    //balloon.setSize(350, 140);
    point.addBalloon(balloon);
    $(point.element).data('uid', object.id);

    AT.map.geometry.add(point);

    PGmap.Events.addHandler(point.element, 'click', function(e) {
        var uid = $(point.element).data('uid');
        $.ajax({
            url: fetchMarkerDetailUri,
            data: { id: uid },
            success: function(res) {
                //console.log( $(point.balloon.element) );
                //$(point.balloon.element).find('span').html($('<div class="w"></div>').append(res));
                $(point.balloon.element)
                    .data('point', point)
                    .html(res);

                point.balloon.show();

            }
        });

    });

};

AT.initHacks = function() {
    $('.g-closer').live('click', function(){
        console.log('try to close bubble');

        $(this).closest('.b-balloon').data('point').hideBalloon();

        return false;
    });
};