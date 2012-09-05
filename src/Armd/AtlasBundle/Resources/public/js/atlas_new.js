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
    AT.initMyObjects();

    // Сабмитим форму (показываем Образы России)
    var elems = $('#atlas-filter-form').find('.gray-checked');
    for (var i=0; i<elems.length; i++) {
        var tagId = $(elems[i]).find('span').data('tag');
        if (tagId == 74) {
            $(elems[i]).find('span').click();
            break;
        }
    }
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
            zoom: params.zoom,
            minZoom: 3
        };

    this.map = new PGmap(map_el, parameters);
    this.map.controls.addControl('slider');

    this.map.balloon.content.parentNode.style.width = '300px';
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

    // Вкладки. Перехват смены вкладок
    $('.filter-tabs-titles li a, .filter-sub-tabs-titles li a').click(function(){
        var href = $(this).attr('href');
        if (href == "#maps") {
            // Переключение на Мои карты. Очистим карту.
            AT.clearMap();
        } else if (href == "#obj") {
            // Отображаем объекты с примененным фильтром
            $('#atlas-filter-form').submit();
        }
        return false;
    });

    // Объекты. Фильтр
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
                    var object = objects[i],
                        point = AT.placePoint(objects[i]);

                    if (point && !object.obraz) {
                        points.push(point);
                    }
                }

                // clusterize points
                if (points.length) {
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

    $('.atlas-filter-form .check_all').click(function(){
        var parentDiv = $(this).closest('.simple-filter-block');
        if (!$(this).data('checked')) {
            parentDiv.find('input:checkbox').attr('checked','checked');
            parentDiv.find('label').addClass('checked');
            $(this).data('checked', true).addClass('checked');
        } else {
            parentDiv.find('input:checkbox').removeAttr('checked');
            parentDiv.find('label').removeClass('checked');
            $(this).data('checked', false).removeClass('checked');

        }
    });

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

AT.placePoint = function(object) {

    if (! (object.lon || object.lat)) {
        //console.log('Coords is not set properly', object);
        return false;
    }

    if (object.obraz) {
        var point = new PGmap.Point({
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
        var point = new PGmap.Point({
                coord: new PGmap.Coord(object.lon, object.lat, true),
                width: 42,
                height: 39,
                backpos: '0 0',
                url: object.icon
            });
    }
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

/**
 * Работа с пользовательскими объектами
 */
AT.initMyObjects = function() {

    // Кнопка-крестик закрывает попап
    $('#add-object-form .exit').click(function(){
        $('#add-object-form').hide();
        return false;
    });

    // Добавить объект на карту
    $('#atlas-objects-add').click(function(e){
        e.preventDefault();
        console.log('Add new point button clicked');

        PGmap.Events.addHandler(AT.map.globals.mainElement(), 'mousedown', onMouseDown);

        function onMouseDown(e) {
            console.log('onMouseDown');
            PGmap.Events.stopEvent(e);
            PGmap.Events.addHandler(this, 'mousemove', onMouseMove);
            PGmap.Events.addHandler(this, 'mouseup', onMouseUp);
        }
        function onMouseMove(e) {
            // console.log('onMouseMove'); // частит слишком. отключил
            //PGmap.Event.killEvent(e);
            //console.log( AT.map.globals.mainElement() );
            PGmap.Events.removeHandler(AT.map.globals.mainElement(), 'mouseup', onMouseUp);
        }
        function onMouseUp(e) {
            console.log('onMouseUp');
            var diff = AT.map.globals.getPosition(),
                e = e || window.event,
                off = PGmap.Utils.getOffset(document.getElementById('map')),
                mousepos = { x: (e.pageX || e.clientX) - off.left, y: (e.pageY || e.clientY) - off.top },
                coord = AT.map.globals.xyToLonLat(mousepos.x - diff.left, mousepos.y - diff.top);
            var point = placePoint(coord, 1, false);
            AT.showAddObjectForm({
                coord: coord,
                point: point
            });
            PGmap.Events.removeHandler(AT.map.globals.mainElement(), 'mousedown', onMouseDown);
        }
        function placePoint(coord, draggable, onClick) {
            // установка точки
            console.log('placePoint');
            var point = new PGmap.Point({
                coord: coord,
                //width: 24,
                //height: 36,
                //url: 'middle_sprite.png',
                //backpos: '-48px 0',
                draggable: draggable
            });

            if (! onClick) {
                //PGmap.Events.addHandler(point.element, 'click', onClick);
            } else {
                PGmap.Events.addHandler(point.container, 'click', function(e) {
                    console.log('click');
                    //e.stopPropagation();
                    e.preventDefault();
                });
            }
            AT.map.geometry.add(point);
            return point;
        }
    });
};

/**
 * Открывает попап с информацией о точке
 * @param params
 */
AT.showAddObjectForm = function(params) {
    var
        myPoint = params.point,
        jPopup = $('#add-object-form'),
        jPopupForm = jPopup.find('form'),
        jSuccess = $('#success-object-form'),
        jSuccessForm = jSuccess.find('form'),
        jMyObjectsList = $('#myobj_list');

    console.log('Show dialog for point:', myPoint);

    // Fill form
    jPopupForm.resetForm();
    $('#lon').val(PGmap.Utils.fromMercX(params.coord.lon));
    $('#lat').val(PGmap.Utils.fromMercY(params.coord.lat));

    jPopup.show();

    // Диалог добавления объекта. Кнопка отменить. Скрываем диалог
    jPopup.find('.rst-btn, .exit').click(function(){
        jPopup.hide();
        return false;
    });

    // Диалог после добавления объекта. Крестик. Скрываем диалог.
    jSuccess.find('.exit').click(function(){
        jSuccess.hide();
        return false;
    });

    // Отправка данных. Добавить объект.
    jPopupForm.ajaxForm({
        dataType: 'json',
        beforeSubmit: function(){
            $('#ajax-loading').show();
        },
        success: function(response, statusText, xhr, $form){
            $('#ajax-loading').hide();
            if (response.success) {
                var createdObject = response.result;
                jSuccess.find('.object-id').val(createdObject.id);
                jSuccess.find('.object-title').val(createdObject.title);
                jSuccess.show();
            } else {
                alert(response.message);
            }
            jPopup.hide();

            // тут надо отключить перетаскивание точки
            myPoint.draggable.kill();
        }
    });

    // Добавить объект на народную карту.
    jSuccessForm.ajaxForm({
        dataType: 'json',
        beforeSubmit: function(){
            $('#ajax-loading').show();
        },
        success: function(response, statusText, xhr, $form){
            $('#ajax-loading').hide();
            if (response.success) {
                jSuccess.hide();

                objectTitle = response.result.title;

                // Добавляем в список точку
                jMyObjectsList.append($('#myobj_list_template').tmpl({
                    'title': objectTitle
                }));
            }
        }
    });

}
