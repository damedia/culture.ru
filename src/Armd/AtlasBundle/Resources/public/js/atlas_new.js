/**
 * Атлас. Новый
 */
var AT = {};

AT.version = '0.3';
AT.params = {};
AT.map = null;
AT.filterTags = [];
AT.clusterPoints = null;
AT.regions = [];

AT.init = function(params) {
//    console.info('Init Atlas');
    this.params = params;

    AT.initMap(params);
    AT.initGeocoder();
    AT.initUI();
    AT.initFilters();
    AT.initTabs();
    AT.initHacks();
    AT.initMyObjects();

    //AT.selectFirstFilterObject();
};

AT.initMap = function(params) {
    //console.log(AT.params.locale);
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

    this.map.layers.LEFTLIMITLON = 2081245.7489734937;
    this.map.layers.RIGHTLIMITLON = -19736433.1934625;

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

AT.initUI = function() {

//    // Вкладки. Перехват смены вкладок
//    $('.filter-tabs-titles li a, .filter-sub-tabs-titles li a').click(function(){
//        var href = $(this).attr('href');
//        if (href == "#maps") {
//            // Переключение на Мои карты. Очистим карту. Отбить AJAX
//            AT.clearMap();
//            if ($('#map-section').hasClass('logged-in')) {
//                AT.initMyObjects();
//            }
//        }
//        else if (href == "#obj") {
//            // Отображаем объекты с примененным фильтром
//            $('#atlas-filter-form').submit();
//        }
//        $(this).blur();
//        return false;
//    });

    // Объекты. Фильтр
    $('#atlas-filter-form').ajaxForm({
        beforeSubmit: function(){
            //console.log( $('#atlas-filter-form').data('jqxhr') );
            $('#ajax-loading').show();
        },
        success: function(responseText, statusText, xhr, $form){
            $('#ajax-loading').hide();
            // Если вторая вкладка текущая, не рисуем объекты
            //if ($('.filter-tabs-titles li.active').index())
            //    return;

            var elems = $('.atlas-filter-form .tag'),
                json = responseText; //$.parseJSON(responseText);

            if (json.success) {
                var objects = json.result;
                elems.addClass('disabled');
            } else {
                elems.removeClass('disabled');
                //console.error(json.message);
            }

            AT.clearMap();

            for (var i in json.allCategoriesIds) {
                var tag = json.allCategoriesIds[i];
                elems.each(function(i, el){
                    var elSpan = $(el).find('span');
                    var tagId = elSpan.data('tag');
                    if (tagId == tag) {
                        $(el).removeClass('disabled');
                    }
                });
            }

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
                        clusterImage: AT.params.bundleImagesUri + "/klaster_1.1.png"
                    });
                    AT.clusterPoints.setClusterImageByCount = function(count) {
                        return "0px 0px";
                    };
                    AT.clusterPoints.setClusters.callback = function() {
                        for (var n = AT.clusterPoints.clusters.length; n--; ) {
                            (function(cluster){
                                PGmap.Events.addHandlerByName(cluster.element, PGmap.EventFactory.eventsType.click, function(e){

                                    //console.log('click on cluster');

                                    // Балун для кластера
                                    cluster.balloon = AT.map.balloon;

                                    var ids = [];
                                    for (var i=0; i<cluster.points.length; i++) {
                                        ids.push($(cluster.points[i].container).data('uid'));
                                    }
                                    // Содержимое балуна
                                    //cluster.name = ids.join(',');
                                    //console.log('cluster points ids:', cluster.name);

                                    // Если достигнут последний уровень зума, показываем бабл
                                    if ((AT.map.globals.maxZoom() - AT.map.globals.getZoom()) == 1) {
                                        if (! cluster.balloon.element.offsetHeight || cluster.balloon.currEl != cluster ) {

                                            $('#ajax-loading').show();
                                            $.ajax({
                                                url: AT.params.fetchClusterDetailUri,
                                                data: { ids: ids },
                                                success: function(res){
                                                    $('#ajax-loading').hide();
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

            AT.fitMap(objects);
        }
    });

    // Инициализируем список регионов
    var regionsSelector = $('#regions-selector select');
    // При выборе региона, зумим карту к нему
    regionsSelector.chosen({ no_results_text:"Не найдено" }).change(function(){
        AT.map.search({
            q: $(this).find('option:selected').text(),
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

};


AT.selectFirstFilterObject = function() {
    var elems = $('.ajax-filter-tabs').filter(':visible').find('.gray-checked');
    if (elems.filter('.checked').length == 0) {
        $('span', $(elems[0])).click();
    } else {
        AT.submitFiltersForm();
    }
}


// Init filters
AT.initTabs = function() {

//    if (window.location.hash) {
//        var filterType = "filter_culture_objects";
//        var hash = window.location.hash.substr(1).replace("-", "_");
//
//        if ($.inArray(hash, ["culture_objects", "tourist_clusters", "user_objects"]) > -1) {
//            filterType = "filter_" + hash;
//        }
//        AT.showTab(filterType);
//    }

    AT.showTab(AT.params.initialFilterType, true);

    $('.atlas-tab-filters').on( 'click', function(event){
        event.preventDefault();
        var filterType = $(this).attr('id');
        AT.showTab(filterType);
    });
}

AT.showTab = function(filterType, force) {
    if (typeof(force) === 'undefined') {
        force = false;
    }
    if ($('#' + filterType).hasClass('active') && !force) {
        return;
    }

    var filterTabId = AT.getFilterTabIdByFilterType(filterType);

    $('.atlas-tab-filters').removeClass('active');
    $('#' + filterType).addClass('active');
    $('.atlas-side-tab').hide();
    $('#' + filterTabId).show();

    $('#ajax-loading').show();
    $('#filter-type').val(filterType);

    AT.clearMap();

    if (filterType === 'filter_culture_objects' || filterType === 'filter_tourist_clusters') {
        AT.selectFirstFilterObject();
    } else if (filterType === 'filter_user_objects') {
        AT.initMyObjectsTab();
    }

    if (typeof(history.pushState) !== 'undefined') {
        history.pushState(null, document.title, Routing.generate('armd_atlas_index', {'filterType': filterType}));
    }
}

AT.getFilterTabIdByFilterType = function (filterType) {
    var filterTabId = filterType.replace('filter_', '').replace('_', '-') + '-tab';
    return filterTabId;
}


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

    $('.atlas-filter-form').on('click', '.simple-filter-options > label > span', (function(e){
        // перехват обработчика из function.js
        e.preventDefault();
        e.stopPropagation();

        if ($('.content-tabs .active').attr('id') === 'filter_tourist_clusters') {
            $('label', $(this).closest('.simple-filter-options')).removeClass('checked');
        }
        $(this).closest('label').toggleClass('checked');
        // сбор отмеченных тегов
        AT.submitFiltersForm();
    }));

    $('.atlas-filter-form').find('.check_all').click(function(e){
        // сбор отмеченных тегов
        AT.submitFiltersForm();
    });
};

AT.clearMap = function() {
    //console.info('AT.clearMap');
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
                    width: 17
                }
            });
        $(point.container).data('uid',object.id).css({'margin-left':'-4px','margin-top':'-12px'});
    } else {
        var point = new PGmap.Point({
                coord: new PGmap.Coord(object.lon, object.lat, true),
                width: 42,
                height: 39,
                backpos: '0 0',
                url: object.icon
            });
        $(point.container).data('uid',object.id).css({'margin-left':'-12px','margin-top':'-40px'});
    }
    $(point.container).data('uid', object.id).attr('title', object.title);

    AT.map.geometry.add(point);

    PGmap.EventFactory.eventsType.mouseover = 'mouseover';
    PGmap.EventFactory.eventsType.mouseout = 'mouseout';
    // наведение на точку
    PGmap.Events.addHandler(point.container, PGmap.EventFactory.eventsType.mouseover, function(e) {
        var img = $('img', point.container);
        if(img.length){
            img.css({width:50});
            $(point.container).css({marginLeft:-18, marginTop:-24});
        }
    });
    PGmap.Events.addHandler(point.container, PGmap.EventFactory.eventsType.mouseout, function(e) {
        var img = $('img', point.container);
        if(img.length){
            img.css({width:17});
            $(point.container).css({marginLeft:-4, marginTop:-12});
        }
    });
    // клик по точке
    PGmap.Events.addHandler(point.container, PGmap.EventFactory.eventsType.click, function(){
        //console.log('Click point', point);
        if (point.draggable) return;
        AT.triggerPointClick(point);
    });

    return point;
};

// Инициирует клик по точке. Появляется балун
AT.triggerPointClick = function(point) {
    //console.log('triggerPointClick');
    var uid = $(point.container).data('uid');

    $('#ajax-loading').show();
    $.ajax({
        url: AT.params.fetchMarkerDetailUri,
        data: { id: uid },
        success: function(res) {
            //console.log('click on point');
            $('#ajax-loading').hide();
            point.name = res;
            point.balloon = AT.map.balloon;
            point.toggleBalloon(); // тут должна быть задержка
        }
    });
}

AT.initHacks = function() {
    $('.g-closer').live('click', function(){
        //console.log('try to close bubble');
        $(this).closest('.b-balloon').data('point').hideBalloon();
        return false;
    });
    
    $('div#'+this.params.map+' div.PGmap').css({zIndex:0});
};

AT.collectTagsValue = function() {
    var filterTags = [];
    var filterTabId = AT.getFilterTabIdByFilterType($('#filter-type').val());

    $('.atlas-filter-form #' + filterTabId).find('.simple-filter-options > label.checked > span').each(function(i,el){
        filterTags.push( $(this).data('tag') );
    });
    $('#category-id').val(filterTags);
    return filterTags;
};

AT.submitFiltersForm = function() {
    //console.info('AT.submitFiltersForm');
    // Собираем отмеченные категории
    AT.collectTagsValue();
    // Сабмитим форму
    $('#atlas-filter-form').submit();
};

AT.initMyObjects = function() {

    $('#add-object-form .added-images').on('click', '.del', function(){
        if (confirm('Удалить?')) {
            var jImage = $(this).closest('.added-image');
            var imageId = jImage.data('id');
            jImage.remove();
            $.ajax({
                url: Routing.generate('armd_atlas_default_objects_my_delete_image', {'mediaId': imageId})
            });
        }
    });

}

/**
 * Работа с пользовательскими объектами
 */
AT.initMyObjectsTab = function() {

    // Построение списка моих объектов
    $('#ajax-loading').show();
    $.ajax({
        url: AT.params.fetchMyObjectsUri, // /atlas/objects/my
        dataType: 'json',
        success: function(res) {
            AT.clearMap();
            $('#ajax-loading').hide();
            if (res.success) {
                $('#myobj_list').empty();
                for (var i in res.result) {
                    var object = res.result[i];
                    var jLi = $('#myobj_list_template').tmpl(object);
                    var point = AT.placePoint(object);

                    jLi.data('point', point);
                    $('#myobj_list').append(jLi);
                }
                if (AT.params.objectId) {
                    AT.showObjectFormById(AT.params.objectId);
                    AT.params.objectId = null;
                    $('html, body').animate({
                        scrollTop: $("#filter_culture_objects").offset().top
                    }, 500);
                }

            } else {
                alert(res.message);
            }
        },
        complete: function() {
            $('#ajax-loading').hide();
        }
    });

    // Мои карты -> Мои объекты. Клик по элементу списка. map.setCenter
    $('#myobj_list li span').live('click', function(){
        var jLi = $(this).closest('li'),
            point = jLi.data('point');
        //console.log(point.coord);
        AT.map.setCenterFast(point.coord);
        AT.triggerPointClick(point); // @TODO Глючит во время анимации
    });

    // Мои карты -> Мои объекты. Редактирование точки из списка.
    $('#myobj_list li .edit').live('click', function(){
        var jLi = $(this).closest('li'),
            objectId = jLi.data('id'),
            point = jLi.data('point'),
            coord = point.coord;

        // Делаем запрос к бэкенду - получаем данные объекта
        $('#ajax-loading').show();
        $.ajax({
            url: AT.params.fetchMyObjectsUri,
            data: { id:objectId },
            dataType: 'json',
            success: function(res){
                $('#ajax-loading').hide();
                if (res.success) {
                    AT.showObjectForm({
                        entity: res.result,
                        coord: coord,
                        point: point
                    });
                } else {
                    alert(res.message);
                }
            }
        });
    });
    $('.bubble_content .edit').live('click', function(){
        var objectId = $(this).data('id'),
            point = AT.map.balloon.currEl,
            coord = point.coord;

        // Делаем запрос к бэкенду - получаем данные объекта
        $('#ajax-loading').show();
        $.ajax({
            url: AT.params.fetchMyObjectsUri,
            data: { id:objectId },
            dataType: 'json',
            success: function(res){
                $('#ajax-loading').hide();
                if (res.success) {
                    // И показываем попап с формой
                    AT.showObjectForm({
                        entity: res.result, // в режиме редактирования
                        coord: coord,
                        point: point
                    });
                    // Скрываем балун
                    AT.map.balloon.close();
                } else {
                    alert(res.message);
                }
            }
        });
        return false;
    });

    // Мои карты -> Мои объекты. Удаление точки из списка, с карты и вообще.
    $('#myobj_list li .del').live('click', function(){
        if (confirm('Удалить?')) {
            var el = $(this).closest('li'),
                id = el.data('id');

            $('#ajax-loading').show();
            $.ajax({
                dataType: 'json',
                url: AT.params.deleteMyObjectsUri,
                data: { id: id },
                success: function(json){
                    $('#ajax-loading').hide();
                    if (json.success) {
                        var point = el.data('point');
                        el.remove();
                        AT.map.geometry.remove(point);
                    } else {
                        alert(json.message);
                    }
                }
            });
        }
        return false;
    });

    // Отправка объекта на модерацию
    $('#myobj_list .moder').live('click', function(){
        var el = $(this).closest('li'),
            id = el.data('id'),
            statusId = $(this).text(),
            reason = $(this).data('reason');
        $('#moderation-object-id').val(id);

        if (statusId == 1) {
            $('#moderation-object-form-1').show();
        } else if (statusId == 2) {
            $('#moderation-object-form-2').show();
        } else if (statusId == 3) {
            $('#moderation-object-form-3').show();
            $('#moder-reason').text(reason);
        } else {
            $('#moderation-object-form').show();
        }

        $('#moderation-object-form').find('form').ajaxForm({
            dataType: 'json',
            beforeSubmit: function(){
                $('#ajax-loading').show();
            },
            success: function(response, statusText, xhr, $form){
                $('#ajax-loading').hide();
                if (response.success) {
                    //console.log('Смена статуса. Отправлено на модерацию.');
                    var objectId = response.result.id;
                    var status = response.result.status;
                    var jLi = $('#myobj_list li').filter(function(){ return $(this).data('id')==objectId; })
                    jLi.find('.moder').removeClass('status0 status1 status2 status3').addClass('status'+status);
                    jLi.find('.moder span').text(status);
                }
                $('#moderation-object-form').hide();
            }
        });
    });
    $('.moderation-object-form .rst-btn, .moderation-object-form .exit').on('click', function(){
        $(this).closest('.moderation-object-form').hide();
        return false;
    });

    // Добавить объект на карту
    $('#atlas-objects-add').click(function(e){

        $('#atlas-objects-add-hint').show();

        // Иконка для курсора
        $('.PGmap-layer-container').css('cursor', 'url("/bundles/armdatlas/images/cursor_pin.cur") 10 32, move');

        // Если кнопка добавления объекта находится в зажатом состоянии, отжимаем ее и отменяем процесс.
        if ($(this).hasClass('active')) {
            //console.log('Discard add point');

            $(this).removeClass('active');
            $('#add-object-form').hide();
            PGmap.Events.stopEvent(e);

            var droppedPoint = $('#atlas-objects-add').data('droppedPoint');
            if (droppedPoint) {
                //console.log('Remove droppedPoint', droppedPoint);
                AT.map.geometry.remove(droppedPoint);
            }

            // @TODO: Как удалить событие нажатия мыши?
            PGmap.Events.removeHandler(AT.map.globals.mainElement(), 'mousedown', onMouseDown);
            PGmap.Events.removeHandler(AT.map.globals.mainElement(), 'mouseup', onMouseUp);
            return false;
        }
        else {
            $(this).addClass('active');

            //console.log('Activate drop point');

            // При клике на карте запускаем сложный механизм отслеживания событий
            PGmap.Events.addHandler(AT.map.globals.mainElement(), 'mousedown', onMouseDown);
        }

        function onMouseDown(e) {
            //console.log('onMouseDown');
            // Если нажали кнопку мышки
            PGmap.Events.stopEvent(e);
            PGmap.Events.addHandler(AT.map.globals.mainElement(), 'mousemove', onMouseMove);
            PGmap.Events.addHandler(AT.map.globals.mainElement(), 'mouseup', onMouseUp);
        }

        function onMouseMove(e) {
            PGmap.Events.removeHandler(AT.map.globals.mainElement(), 'mouseup', onMouseUp);
        }

        function onMouseUp(e) {
            //console.log('onMouseUp');
            var diff = AT.map.globals.getPosition(),
                e = e || window.event,
                off = PGmap.Utils.getOffset(document.getElementById('map')),
                mousepos = { x: (e.pageX || e.clientX) - off.left, y: (e.pageY || e.clientY) - off.top },
                coord = AT.map.globals.xyToLonLat(mousepos.x - diff.left, mousepos.y - diff.top);

            // Бросили точку на карту
            var point = placePoint(coord, 1, function(){
                // При клике по точке показываем обычный бабл с инфой
                AT.triggerPointClick(point);
                e.preventDefault();
            });

            // Сохраним в кнопке Добавить объект ссылку на созданную точку
            $('#atlas-objects-add').data('droppedPoint', point);

            // Показываем форму создания объекта
            AT.showObjectForm({
                coord: coord,
                point: point
            });

            // Запрещаем ставить точки, пока не закончим с текущей
            PGmap.Events.removeHandler(AT.map.globals.mainElement(), 'mousedown', onMouseDown);
        }

        function placePoint(coord, draggable, onClick) {
            //console.log('placePoint');
            // установка точки
            var point = new PGmap.Point({
                draggable: draggable,
                coord: coord
            });

            if (draggable) {
                // Draggable point handler -> update coords
                point.draggable.callback("dragEnd", function(){
                    //console.log('dragEnd');
                    var lon = PGmap.Utils.fromMercX(point.coord.lon).toFixed(6),
                        lat = PGmap.Utils.fromMercY(point.coord.lat).toFixed(6);
                    $('#lon').val(lon);
                    $('#lat').val(lat);

                    // Попытка определить адрес по координатам
                    //console.log('Попытка определить адрес по координатам');
                    AT.map.search({
                        lon: point.coord.lon,
                        lat: point.coord.lat,
                        type: 'geocode',
                        lng: AT.params.locale
                    }, function (r) {
                        var data = JSON.parse(r).res[0];
                        $('#address').val(data.addr);
                    });
                });
            }

            if (onClick) {
                //PGmap.Events.addHandler(point.container, 'click', onClick);
            }
            AT.map.geometry.add(point);
            return point;
        }
    });

    // Диалог добавления объекта. Кнопка отменить. Скрываем диалог
    var jPopup = $('#add-object-form');
    jPopup.find('.rst-btn, .exit').click(function(){
        //console.log('Сancel edit point');

        $('#atlas-objects-add').removeClass('active');
        $('#atlas-objects-add-hint').hide();

        jPopup.hide();
        myPoint = jPopup.data('myPoint');
        if (myPoint.draggable) {
            // Отключаем перетаскивание точки. Восстанавливаем исходные координаты и скрываем балун
            //console.log('Kill draggable', myPoint);
            myPoint.draggable.kill();
            myPoint.draggable = null;
            myPoint.coord = jPopup.data('myPointCoord');
            myPoint.update();
            if (myPoint.balloon) {
                myPoint.balloon.close();
            }
        }
        if (jPopup.hasClass('add')) {
            AT.map.geometry.remove(myPoint); // Удаляем точку с карты если диалог в режиме добавления
        }
        return false;
    });

};

/**
 * Открывает попап с информацией о точке
 */
AT.showObjectForm = function(params) {
    //console.log('showObjectForm', params);
    var
        myPoint = params.point,
        jPopup = $('#add-object-form'),
        jPopupForm = jPopup.find('form'),
        jSuccess = $('#success-object-form'),
        jSuccessForm = jSuccess.find('form'),
        jMyObjectsList = $('#myobj_list'),
        jAddedImages = jPopup.find('.added-images');

    //console.log('Show dialog for point:', myPoint);
    jPopup.data('myPoint', myPoint);
    jPopup.data('myPointCoord', myPoint.coord); // Сохраним координаты точки, на случай отмены редактирования

    // Reset form
    jPopupForm.resetForm();
    jAddedImages.empty();

    // Открываем попап
    jPopup.show();

    // Список категорий в диалоге
    $('#primary-category, #category').select2();
    /* $('#primary-category').change(function(){
        console.log('Change primary category for', $(myPoint.container));
        $(myPoint.container).css({
            'background-position': '0 0',
            'background-image': 'url(http://api-maps.yandex.ru/2.0.14/release/../images/a19ee1e1e845c583b3dce0038f66be2b)'
        });
        //myPoint.url = 'http://api-maps.yandex.ru/2.0.14/release/../images/a19ee1e1e845c583b3dce0038f66be2b';
        //myPoint.update();
    }); */

    // Заполняем форму
    if (params.entity) {
        jPopup.removeClass('add').addClass('edit');
        $('#object-id').val(params.entity.id);
        $('#name').val(params.entity.title);
        $('#address').val(params.entity.address);
        $('#descr').val(params.entity.announce);
        $('#lon').val(params.entity.lon);
        $('#lat').val(params.entity.lat);
        $('#primary-category').select2('val', params.entity.primaryCategory);
        $('#category').select2('val', params.entity.secondaryCategory);


        if (params.entity.images.length > 0) {
            for (var i in params.entity.images) {
                var image = params.entity.images[i];
                $('#added-image-template')
                    .tmpl({
                        'id': image.id,
                        'imageUrl': image.thumbUrl
                    })
                    .appendTo('#add-object-form .added-images');
            }

        }

        // Включим перетаскивание точки
        var point = myPoint;
        point.draggable = new PGmap.Events.Draggable(point, {
            drag: function (pos) {
                point.coord = point.globals.xyToLonLat(pos.x, pos.y);
                var lon = PGmap.Utils.fromMercX(point.coord.lon).toFixed(6),
                    lat = PGmap.Utils.fromMercY(point.coord.lat).toFixed(6);
                $('#lon').val(lon);
                $('#lat').val(lat);
            },
            dragEnd: function (pos) {
                // Попытка определить адрес по координатам
                //console.log('Попытка определить адрес по координатам');
                AT.map.search({
                    lon: point.coord.lon,
                    lat: point.coord.lat,
                    type: 'geocode',
                    lng: AT.params.locale
                }, function (r) {
                    var data = JSON.parse(r).res[0];
                    $('#address').val(data.addr);
                });
            }
        });

    }
    else {
        jPopup.removeClass('edit').addClass('add');
        $('#object-id').val('');
        $('#lon').val(PGmap.Utils.fromMercX(params.coord.lon).toFixed(6));
        $('#lat').val(PGmap.Utils.fromMercY(params.coord.lat).toFixed(6));
    }

    //console.log('myPoint', myPoint);

    // Диалог после добавления объекта. Крестик. Скрываем диалог.
    jSuccess.find('.exit').click(function(){
        $('#atlas-objects-add').removeClass('active');
        jSuccess.hide();
        if (myPoint.draggable) {
            myPoint.draggable.kill(); // Отключение перетаскивания точки
        }
        return false;
    });

    // Адрес. Геолокация
    $('#address').autocomplete({
        source: function(request, response) {
            AT.map.search({
                q: request.term,
                type: 'search'
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
            var lon = parseFloat(ui.item.match.x),
                lat = parseFloat(ui.item.match.y);
            $('#lon').val(lon.toFixed(6));
            $('#lat').val(lat.toFixed(6));
            myPoint.coord.lon = PGmap.Utils.mercX(lon);
            myPoint.coord.lat = PGmap.Utils.mercY(lat);
            myPoint.update();
            AT.map.setCenter(new PGmap.Coord(lon, lat, true));
        }
    }).data('autocomplete')._renderItem = function(ul, item) {
        return $("<li></li>")
            .data("item.autocomplete", item)
            .append('<a>' + item.label + '</a>')
            .appendTo(ul);
    }

    // Отправка данных. Добавить/обновить объект.
    jPopupForm.ajaxForm({
        dataType: 'json',
        beforeSubmit: function(){
            $('#ajax-loading').show();
        },
        success: function(response, statusText, xhr, $form){
            $('#ajax-loading').hide();
            if (response.success) {
                jPopup.hide(); // Прячем диалог добавления точки

                if (myPoint.draggable) {
                    myPoint.draggable.kill(); // Отключение перетаскивания точки
                }

                var createdObject = response.result;
                AT.map.geometry.remove(myPoint);

                // Объект создан. Сменить иконку и повесить обработчик показа балуна
                myPoint = AT.placePoint(createdObject);

                // Показываем второй попап с модерацией (created|updated)
                if (response.result.mode == 'edit') {
                    jSuccess.removeClass('add').addClass('edit');
                } else if (response.result.mode == 'add') {
                    jSuccess.removeClass('edit').addClass('add');
                }
                jSuccess.show();
                jSuccess.find('input[name=is_public]').removeAttr('checked');
                jSuccess.find('.object-id').val(createdObject.id);
                jSuccess.find('.object-title').val(createdObject.title);

            } else {
                alert(response.message);
            }
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
                $('#atlas-objects-add').removeClass('active');
                $('#atlas-objects-add-hint').hide();
                jSuccess.hide();

                //console.log('Popup mode', jPopup.hasClass('edit') );

                if (jPopup.hasClass('add')) {
                    // Добавляем в список точку
                    var objectId = response.result.id,
                        objectTitle = response.result.title,
                        status = response.result.status,
                        statusTitle = response.result.statusTitle,
                        jLi = $('#myobj_list_template').tmpl({ 'title': objectTitle });

                    // Связываем точку с элементом списка
                    jLi.data('id', objectId),
                    jLi.data('point', myPoint);
                    jLi.find('.moder').removeClass('status0 status1 status2 status3')
                                      .addClass('status'+status)
                                      .attr('title', statusTitle);
                    jLi.find('.moder span').text(status);
                    jMyObjectsList.append(jLi);
                } else {
                    // Обновляем название точки в списке
                    var objectId = $('#object-id').val(),
                        status = response.result.status,
                        statusTitle = response.result.statusTitle,
                        jLi = jMyObjectsList.find('li').filter(function(){ return $(this).data('id')==objectId; });
                    jLi.find('span').text($('#name').val());
                    jLi.find('.moder span').text(response.result.status);
                    jLi.find('.moder').removeClass('status0 status1 status2 status3')
                                      .addClass('status'+status)
                                      .attr('title', statusTitle);
                }
                //console.log('jSuccessForm.ajaxForm.success()');
            }
        }
    });

    // Ajax file uploader
    var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader'),
        action: AT.params.imageUploadUri, // /objects/my/upload
        template: '<div class="qq-uploader">'
                + '  <div class="qq-upload-drop-area" style="display:none;"><span>Перетащите файлы сюда</span></div>'
                + '  <div class="qq-upload-button">Загрузить фото&hellip;</div>'
                + '  <ul class="qq-upload-list">Загрузить фото&hellip;</ul>'
                + '</div>',
        onSubmit: function(id, name) {
            armdMk.startLoading();
        },
        onComplete: function(id, filename, response) {
            armdMk.stopLoading();
            if (response.success) {
                var jImageTemplate = $('#added-image-template').tmpl(response.result);
                jAddedImages.append(jImageTemplate);
//                jImageTemplate.find('.del').on('click', function(){
//                    if (confirm('Удалить?')) {
//                        $(this).closest('.added-image').remove();
//                        // @TODO: И с сервака удалить тоже
//                    }
//                });
            } else {
                alert(response.message);
            }
        }
    });

};

AT.showObjectFormById = function(objectId) {
    $('#myobj_list li[data-id=' + objectId + '] > span').trigger('click');
};

/**
 * Fit map.
 */
AT.fitMap = function(objects) {
    if (typeof(objects) !== 'undefined' && objects.length) {
        var latArr = [],
            lonArr = [];

        for (var x in objects) {
            var object = objects[x];

            lonArr.push(object.lon);
            latArr.push(object.lat);
        }

        AT.map.setCenterByBbox({
            lon1: PGmap.Utils.mercX(Math.min.apply(null, lonArr)),
            lon2: PGmap.Utils.mercX(Math.max.apply(null, lonArr)),
            lat1: PGmap.Utils.mercY(Math.min.apply(null, latArr)),
            lat2: PGmap.Utils.mercY(Math.max.apply(null, latArr))
        });

    } else {
        AT.map.setCenter(new PGmap.Coord(AT.params.center[0], AT.params.center[1], true), AT.params.zoom);
    }
};
