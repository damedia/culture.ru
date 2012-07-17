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
    AT.initUI();

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
            console.log('Try to search:', searchTerm);

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

    $("#filter").accordion({ header: "h3", autoHeight: false});

    /*Цикл по всем чекбоксам*/
    $("#filter ul:first li input:checkbox").each(function(){
        /*Если у чекбокса есть дочерние чекбоксы*/
        if ($(this).closest('li').find('ul li input:checkbox').length > 0){
            /*Вставляем перед родительским чекбоксом треугольник для раскрытия дочерних чекбоксов*/
            $(this).before('<span style="float: left;" class="ui-icon ui-icon-triangle-1-e">');
            /*Скрываем дочерние чекбоксы*/
            $(this).closest('li').children('ul:first').hide();
        }else
        /*Иначе сдвигаем чекбокс на ширину треугольника (чтоб было ровно)*/
            $(this).css('margin-left', '20px');
    });

    /*ПРи клике по треугольнику*/
    $("#filter ul:first li span").click(function(){
        /*В зависимости от того, видны ли дочерние чекбоксы
         меняем класс треугольника (смотрит в бок - смотрит вниз (треугольник заимствуем у jquery-ui))
         */
        if ($(this).closest('li').children('ul:first').is(':visible')){
            $(this).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
        }else{
            $(this).removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
        }

        /*Меняем видимость списка дочерних чекбоксов на противоположную*/
        $(this).closest('li').children('ul:first').toggle('slow');
    });

    /*При клике по чекбоксу*/
    $("#filter ul li input:checkbox").click(function(){
        /*Если чекбокс выделен - выделяем дочерние, если нет - снимаем выделение у дочених*/
        if ($(this).attr('checked') == 'checked')
            $(this).closest('li').find('ul li input:checkbox').attr('checked', 'checked');
        else
            $(this).closest('li').find('ul li input:checkbox').attr('checked', false);
    });

}