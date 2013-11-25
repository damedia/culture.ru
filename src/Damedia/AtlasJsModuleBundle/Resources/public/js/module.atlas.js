var ATLAS_MODULE = (function(){
    var atlas = {},

        default_locale = 'ru',
        default_focusX = 55,
        default_focusY = 56,
        default_zoom = 4,

        warn_unableToInitAtlasModule = 'Unable to init ATLAS_MODULE:\n',
        warn_unableToInitLocationFinder = 'Unable to init location finder autocomplete:\n',
        warn_unableToInitFilters = 'Unable to init filters:\n',

        pgMap,
        pgMap_locale,
        filterType,
        sendFiltersUrl = Routing.generate('armd_atlas_send_filters'),
        clusterPoints,
        mapClusterImagesUrl,
        fetchPointDetailsUrl = Routing.generate('armd_atlas_fetch_point_details'),
        fetchMyObjectsUrl = Routing.generate('armd_atlas_default_objectsmy'),
        imageUploadUrl = Routing.generate('armd_atlas_default_objectsmyupload'),
        deleteMyObjectsUrl = Routing.generate('armd_atlas_default_objectsmydelete'),
        givenObjectId,
        localeTiles = {
            'en': [
                'http://h01.tiles.tmcrussia.com/map_en/', 'http://h02.tiles.tmcrussia.com/map_en/', 'http://h03.tiles.tmcrussia.com/map_en/',
                'http://h04.tiles.tmcrussia.com/map_en/', 'http://h05.tiles.tmcrussia.com/map_en/', 'http://h06.tiles.tmcrussia.com/map_en/',
                'http://h07.tiles.tmcrussia.com/map_en/'
            ],
            'ru': [
                'http://h01.tiles.tmcrussia.com/map/', 'http://h02.tiles.tmcrussia.com/map/', 'http://h03.tiles.tmcrussia.com/map/',
                'http://h04.tiles.tmcrussia.com/map/', 'http://h05.tiles.tmcrussia.com/map/', 'http://h06.tiles.tmcrussia.com/map/',
                'http://h07.tiles.tmcrussia.com/map/'
            ]
        },
        ObjectCustomEvent = function(){
            var eventHandler;

            this.subscribe = function(callback){
                if (typeof callback === 'function') {
                    eventHandler = callback;
                }
            };

            this.fire = function(sender, eventArgs){
                if (eventHandler) {
                    eventHandler(sender, eventArgs);
                }
            };

            return this;
        },
        updateFilters = new ObjectCustomEvent();

    //TODO: Default options refactoring required.
    function initProGorodMap(options) {
        var placeholderId = options && options.placeholderId ? options.placeholderId.toString() : '',
            locale = options && options.locale ? options.locale : default_locale,
            focusX = options && options.focusX ? options.focusX : default_focusX,
            focusY = options && options.focusY ? options.focusY : default_focusY,
            zoom = options && options.zoom ? options.zoom : default_zoom,

            placeholder = document.getElementById(placeholderId),
            tilesArray = localeTiles[locale],
            coordinates = new PGmap.Coord(focusX, focusY, true),
            pgMap_locale = locale.toUpperCase();

        if (!placeholder) {
            console.warn(warn_unableToInitAtlasModule + 'Couldn\'t find a placeholder for pro-gorod map!\nParameter \'placeholderId\' is incorrect or undefined!');
            return;
        }

        if (!options || !options.mapClusterImagesUrl) {
            console.warn(warn_unableToInitAtlasModule + 'Couldn\'t find pro-gorod map cluster images URL!\nParameter \'mapClusterImagesUrl\' is incorrect or undefined!');
            return;
        }
        else {
            mapClusterImagesUrl = options.mapClusterImagesUrl;
        }

        pgMap = new PGmap(placeholder, {
            roundRobin: { tiles: tilesArray },
            coord: coordinates,
            zoom: zoom,
            minZoom: 3,
            lang: pgMap_locale
        });
        pgMap.controls.addControl('slider'); //add "+" and "-" icons on the map

        //TODO: I don't know what this "baloon" thing is! Thus it's commented out for now.
        //pgMap.balloon.content.parentNode.style.width = '300px';

        updateFilters.subscribe(tabsChangedCallback);
    }

    //TODO: Default options refactoring required.
    function initLocationFinderAc(options) {
        var placeholderId = options && options.placeholderId ? options.placeholderId.toString() : '',

            placeholder = document.getElementById(placeholderId);

        if (!placeholder) {
            console.warn(warn_unableToInitLocationFinder + 'Couldn\'t find a placeholder for location finder autocomplete!\nParameter \'placeholderId\' is incorrect or undefined!');
            return;
        }

        if (typeof $.ui.autocomplete !== 'function') {
            console.warn(warn_unableToInitLocationFinder + 'Couldn\'t find jQuery UI autocomplete module!');
            return;
        }

        $(placeholder).autocomplete({
            source: function(request, response) {
                pgMap.search({ q: request.term, type: 'search', lng: pgMap_locale }, function(mapResponse){
                    var json = $.parseJSON(mapResponse),
                        results = [];

                    if (json.success && (json.res.length > 0)) {
                        $.each(json.res, function(){
                            var element = this;

                            if (element.type === 'addr') {
                                $.each(element.matches, function(){
                                    var match = this;

                                    results.push({
                                        label: match.addr,
                                        match: match
                                    });
                                });
                            }
                        });

                        response(results);
                    }
                });
            },
            select: function (event, ui) {
                var element = ui.item.match,
                    coordinates = new PGmap.Coord(element.x, element.y, true);

                pgMap.setCenter(coordinates, element.zoom);
            }
        });
    }

    //TODO: Default options refactoring required.
    function initFilters(options) {
        var tabs_placeholderId = options && options.tabs_placeholderId ? options.tabs_placeholderId.toString() : '',
            filters_placeholderId = options && options.filters_placeholderId ? options.filters_placeholderId.toString() : '',

            tabs_placeholder = document.getElementById(tabs_placeholderId),
            filters_placeholder = document.getElementById(filters_placeholderId),
            tabLinks,
            tabPanels;

        if (!tabs_placeholder) {
            console.warn(warn_unableToInitFilters + 'Couldn\'t find a tabs placeholder for filters!\nParameter \'tabs_placeholderId\' is incorrect or undefined!');
            return;
        }

        if (!filters_placeholderId) {
            console.warn(warn_unableToInitFilters + 'Couldn\'t find a filters placeholder for filters!\nParameter \'filters_placeholder\' is incorrect or undefined!');
            return;
        }

        tabLinks = $(tabs_placeholder).find('a');
        filterType = tabLinks.filter('.active').attr('id');
        tabPanels = $(filters_placeholder).children('div');

        $.each(tabPanels, function(){
            var tab = $(this),
                tabFilters = $('label', tab);

            if (tab.attr('data-type') !== filterType) {
                tab.hide();
            }

            tabFilters.on('click', function(){
                var item = $(this);

                switch (filterType) {
                    case 'filter_culture_objects':
                        item.hasClass('checked') ? item.removeClass('checked') : item.addClass('checked');
                        break;

                    case 'filter_tourist_clusters':
                        tabFilters.removeClass('checked');
                        item.addClass('checked');
                        break;

                    case 'filter_user_objects':
                        //
                        break;

                    default:
                        console.warn('Error binding filters click handlers: Unknown filter type (' + filterType + ')!');

                }

                updateFilters.fire(undefined, {
                    category: gatherActiveTabFilters_formatted(tab),
                    filterType: filterType });
            });
        });

        tabLinks.on('click', function(event){
            var clickedItem = $(this);

            event.preventDefault();

            if (clickedItem.hasClass('active')) {
                return;
            }

            filterType = clickedItem.attr('id');

            tabLinks.removeClass('active');
            clickedItem.addClass('active');

            tabPanels.hide();
            tabPanels.filter('[data-type="' + filterType + '"]').show();

            if (typeof history.pushState !== 'undefined') {
                history.pushState(null, document.title, Routing.generate('armd_atlas_index', { filterType: filterType }));
            }

            updateFilters.fire(undefined, {
                             category: gatherActiveTabFilters_formatted(tabPanels.filter('[data-type="' + filterType + '"]')),
                             filterType: filterType });
        });

        updateFilters.fire(undefined, {
                         category: gatherActiveTabFilters_formatted(tabPanels.filter('[data-type="' + filterType + '"]')),
                         filterType: filterType });
    }

    function initHacks() { //TODO: don't know what is this... these things exist on the page but invisible
        $('.g-closer').live('click', function(){
            $(this).closest('.b-balloon').data('point').hideBalloon();
            return false;
        });

        $('div#map div.PGmap').css({ zIndex:0 });
    }


    /*===================================
    ==== TODO: awaiting refactoring ===*/
    function tabsChangedCallback(sender, eventArgs){
        showLoadingGif();
        clearMap();

        $.post(sendFiltersUrl, { category: eventArgs.category, filter_type: eventArgs.filterType })
            .done(function(json, textStatus, jqXHR){
                var userObjectsList,
                    userObjectsListTemplate,
                    currentTab = $('.ajax-filter-tabs:visible'),
                    jLi,
                    objects = json.result,
                    points = [],
                    object,
                    point,
                    i,
                    n;

                if (json.success === false) {
                    console.warn('Sending filters failed with response: ' + json.message);
                    return;
                }

                if (objects && objects.length) {
                    if ((filterType === 'filter_culture_objects') || (filterType === 'filter_tourist_clusters')) {
                        for (i in objects) { //TODO: duplication for cycle
                            if (objects.hasOwnProperty(i)) {
                                object = objects[i];
                                point = placePoint(object);

                                if (point && !object.obraz) {
                                    points.push(point);
                                }
                            }
                        }

                        //cluster points
                        if (points.length) {
                            clusterPoints = new PGmap.GeometryLayer({
                                points: points,
                                clusterSize: 100,
                                clusterImage: mapClusterImagesUrl + "/klaster_1.1.png"
                            });

                            clusterPoints.setClusterImageByCount = function(count){
                                return "0px 0px";
                            };

                            clusterPoints.setClusters.callback = function(){
                                for (n = clusterPoints.clusters.length; n--; ) {
                                    (function(cluster){
                                        //cluster balloon on cluster click
                                        PGmap.Events.addHandlerByName(cluster.element, PGmap.EventFactory.eventsType.click, function(e){
                                            var ids = [];

                                            cluster.balloon = pgMap.balloon;

                                            for (var i=0; i<cluster.points.length; i++) {
                                                ids.push($(cluster.points[i].container).data('uid'));
                                            }

                                            clusterPoints.globals.mapObject().setCenterByBbox(cluster.bbox);
                                        }, 'click_' + cluster.index);

                                        PGmap.Events.addHandlerByName(cluster.element, 'mouseover', function(e){
                                            //
                                        }, 'mouseover_' + cluster.index);

                                        PGmap.Events.addHandlerByName(cluster.element, 'mouseout', function(e){
                                            //
                                        }, 'mouseout_' + cluster.index);
                                    })(clusterPoints.clusters[n]);
                                }
                            };

                            clusterPoints.setClusters();
                        }

                        if (filterType === 'filter_culture_objects') {
                            $('p.check_all', currentTab).click(function(){
                                var parentDiv = $(this).closest('.simple-filter-block');

                                if (!$(this).data('checked')) {
                                    parentDiv.find('input:checkbox').attr('checked','checked');
                                    parentDiv.find('label').addClass('checked');
                                    $(this).data('checked', true).addClass('checked');
                                }
                                else {
                                    parentDiv.find('input:checkbox').removeAttr('checked');
                                    parentDiv.find('label').removeClass('checked');
                                    $(this).data('checked', false).removeClass('checked');
                                }

                                updateFilters.fire(undefined, {
                                    category: gatherActiveTabFilters_formatted(currentTab),
                                    filterType: filterType });
                            });
                        }
                    }
                    else {
                        userObjectsList = $('#myobj_list');
                        userObjectsListTemplate = $('#myobj_list_template');
                        userObjectsList.empty();

                        for (i in objects) { //TODO: duplication for cycle
                            if (objects.hasOwnProperty(i)) {
                                object = objects[i];
                                point = placePoint(object);

                                jLi = userObjectsListTemplate.tmpl(object);
                                jLi.data('point', point);
                                userObjectsList.append(jLi);
                            }
                        }

                        if (givenObjectId) {
                            userObjectsList.find('li[data-id=' + givenObjectId + '] > span').trigger('click');
                            givenObjectId = undefined;
                            $('html, body').animate({ scrollTop: $("#filter_culture_objects").offset().top }, 500);
                        }
                    }
                }

                //'edit' button inside the balloon
                $('.bubble_content .edit').live('click', function(){
                    var objectId = $(this).data('id'),
                        point = pgMap.balloon.currEl,
                        coord = point.coord;

                    showLoadingGif();

                    $.ajax({
                        url: fetchMyObjectsUrl,
                        data: { id: objectId },
                        dataType: 'json',
                        success: function(res){
                            hideLoadingGif();

                            if (res.success) {
                                showObjectForm({
                                    entity: res.result,
                                    coord: coord,
                                    point: point
                                });

                                pgMap.balloon.close();
                            }
                            else {
                                alert(res.message);
                            }
                        }
                    });

                    return false;
                });

                //TODO: function name is probably not good; some things inside this will only apply for 'user_objects' tab
                initMyObjectsTab(); //this is for proper acting trough objects balloon forms

                fitMap(objects);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.warn('AJAX request failed with response: ' + textStatus + ' ' + jqXHR.status + ' (' + errorThrown + ')');
            })
            .always(function(){
                hideLoadingGif();
            });
    }

    function fitMap(objects) {
        if (typeof(objects) !== 'undefined' && objects.length) {
            var latArr = [],
                lonArr = [];

            for (var x in objects) {
                var object = objects[x];

                lonArr.push(object.lon);
                latArr.push(object.lat);
            }

            pgMap.setCenterByBbox({
                lon1: PGmap.Utils.mercX(Math.min.apply(null, lonArr)),
                lon2: PGmap.Utils.mercX(Math.max.apply(null, lonArr)),
                lat1: PGmap.Utils.mercY(Math.min.apply(null, latArr)),
                lat2: PGmap.Utils.mercY(Math.max.apply(null, latArr))
            });

        }
    }

    function placePoint(object) {
        var point;

        if (!(object.lon || object.lat)) {
            return false;
        }

        if (object.obraz) {
            point = new PGmap.Point({
                coord: new PGmap.Coord(object.lon, object.lat, true),
                width: 42,
                height: 39,
                backpos: '0 0',
                innerImage: { src: object.imageUrl, width: 17 }
            });

            $(point.container).data('uid',object.id).css({ 'margin-left': '-4px', 'margin-top': '-12px' });
        }
        else {
            point = new PGmap.Point({
                coord: new PGmap.Coord(object.lon, object.lat, true),
                width: 42,
                height: 39,
                backpos: '0 0',
                url: object.icon
            });

            $(point.container).data('uid',object.id).css({ 'margin-left': '-12px', 'margin-top': '-40px' });
        }

        $(point.container).data('uid', object.id).attr('title', object.title);
        pgMap.geometry.add(point);

        PGmap.EventFactory.eventsType.mouseover = 'mouseover';
        PGmap.EventFactory.eventsType.mouseout = 'mouseout';

        //point mouseover
        PGmap.Events.addHandler(point.container, PGmap.EventFactory.eventsType.mouseover, function(e){
            var img = $('img', point.container);

            if (img.length) {
                img.css({ 'width': '50px' });
                $(point.container).css({ 'margin-left': '-18px', 'margin-top': '-24px' });
            }
        });

        //point mouseout
        PGmap.Events.addHandler(point.container, PGmap.EventFactory.eventsType.mouseout, function(e){
            var img = $('img', point.container);

            if (img.length) {
                img.css({ 'width': '17px' });
                $(point.container).css({ 'margin-left': '-4px', 'margin-top': '-12px' });
            }
        });

        //point click
        PGmap.Events.addHandler(point.container, PGmap.EventFactory.eventsType.click, function(){
            triggerPointClick(point);
        });

        return point;
    }

    function triggerPointClick(point) {
        if (point.draggable) {
            return;
        }

        showLoadingGif();

        $.ajax({
            url: fetchPointDetailsUrl,
            data: { id: $(point.container).data('uid') },
            success: function(res) {
                point.name = res;
                point.balloon = pgMap.balloon;
                point.toggleBalloon();

                hideLoadingGif();
            }
        });
    }

    function clearMap() {
        var points = pgMap.geometry.get({ type:'points' });

        if (points.length > 0) {
            for (var i=points.length; i--; ) {
                pgMap.geometry.remove(points[i]);
            }

            if (clusterPoints) {
                for (var n = clusterPoints.clusters.length; n--; ) {
                    (function(cluster){
                        PGmap.Events.removeHandlerByName(cluster.element, 'click',  'click_' + cluster.index);
                        PGmap.Events.removeHandlerByName(cluster.element, 'mouseover', 'mouseover_' + cluster.index);
                        PGmap.Events.removeHandlerByName(cluster.element, 'mouseout',  'mouseout_' + cluster.index);
                    })(clusterPoints.clusters[n]);
                }

                clusterPoints.removeClusters();
            }
        }
    }

    function initMyObjectsTab() {
        var myObjlist = $('#myobj_list'),
            jPopup = $('#add-object-form');

        //click on myObject list item
        myObjlist.find('li span').live('click', function(){
            var jLi = $(this).closest('li'),
                point = jLi.data('point');

            pgMap.setCenterFast(point.coord);
            triggerPointClick(point);
        });

        //click on myObject edit list item
        myObjlist.find('li .edit').live('click', function(){
            var jLi = $(this).closest('li'),
                objectId = jLi.data('id'),
                point = jLi.data('point'),
                coord = point.coord;

            showLoadingGif();

            $.ajax({
                url: fetchMyObjectsUrl,
                data: { id: objectId },
                dataType: 'json',
                success: function(res){
                    hideLoadingGif();

                    if (!res.success) {
                        alert(res.message);
                    }

                    showObjectForm({
                        entity: res.result,
                        coord: coord,
                        point: point
                    });
                }
            });
        });

        //click on myObject delete list item
        myObjlist.find('li .del').live('click', function(){
            if (confirm('Удалить?')) {
                var el = $(this).closest('li'),
                    id = el.data('id');

                showLoadingGif();

                $.ajax({
                    dataType: 'json',
                    url: deleteMyObjectsUrl,
                    data: { id: id },
                    success: function(json){
                        var point;

                        hideLoadingGif();

                        if (json.success) {
                            point = el.data('point');
                            el.remove();
                            pgMap.geometry.remove(point);
                        }
                        else {
                            alert(json.message);
                        }
                    }
                });
            }

            return false;
        });

        // Отправка объекта на модерацию
        myObjlist.find('.moder').live('click', function(){
            var el = $(this).closest('li'),
                id = el.data('id'),
                statusId = $(this).text(),
                reason = $(this).data('reason'),
                moderationObjectForm = $('#moderation-object-form');

            $('#moderation-object-id').val(id);

            if (statusId == 1) {
                $('#moderation-object-form-1').show();
            }
            else if (statusId == 2) {
                $('#moderation-object-form-2').show();
            }
            else if (statusId == 3) {
                $('#moderation-object-form-3').show();
                $('#moder-reason').text(reason);
            }
            else {
                moderationObjectForm.show();
            }

            moderationObjectForm.find('form').ajaxForm({
                dataType: 'json',
                beforeSubmit: function(){
                    showLoadingGif();
                },
                success: function(response, statusText, xhr, $form){
                    hideLoadingGif();

                    if (response.success) {
                        var objectId = response.result.id,
                            status = response.result.status,
                            jLi = myObjlist.find('li').filter(function(){
                                return $(this).data('id')==objectId;
                            });

                        jLi.find('.moder').removeClass('status0 status1 status2 status3').addClass('status' + status);
                        jLi.find('.moder span').text(status);
                    }

                    moderationObjectForm.hide();
                }
            });
        });

        $('.moderation-object-form .rst-btn, .moderation-object-form .exit').on('click', function(){
            $(this).closest('.moderation-object-form').hide();

            return false;
        });

        //add pgMap object
        $('#atlas-objects-add').click(function(e){
            var droppedPoint;

            $('#atlas-objects-add-hint').show();
            $('.PGmap-layer-container').css('cursor', 'url("/bundles/armdatlas/images/cursor_pin.cur") 10 32, move');

            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
                $('#add-object-form').hide();
                PGmap.Events.stopEvent(e);
                droppedPoint = $('#atlas-objects-add').data('droppedPoint');

                if (droppedPoint) {
                    pgMap.geometry.remove(droppedPoint);
                }

                PGmap.Events.removeHandler(pgMap.globals.mainElement(), 'mousedown', onMouseDown);
                PGmap.Events.removeHandler(pgMap.globals.mainElement(), 'mouseup', onMouseUp);

                return false;
            }
            else {
                $(this).addClass('active');
                PGmap.Events.addHandler(pgMap.globals.mainElement(), 'mousedown', onMouseDown);
            }

            function onMouseDown(e) {
                PGmap.Events.stopEvent(e);
                PGmap.Events.addHandler(pgMap.globals.mainElement(), 'mousemove', onMouseMove);
                PGmap.Events.addHandler(pgMap.globals.mainElement(), 'mouseup', onMouseUp);
            }

            function onMouseMove(e) {
                PGmap.Events.removeHandler(pgMap.globals.mainElement(), 'mouseup', onMouseUp);
            }

            function onMouseUp(event) {
                var diff = pgMap.globals.getPosition(),
                    e = event || window.event,
                    off = PGmap.Utils.getOffset(document.getElementById('map')),
                    mousepos = { x: (e.pageX || e.clientX) - off.left, y: (e.pageY || e.clientY) - off.top },
                    coord = pgMap.globals.xyToLonLat(mousepos.x - diff.left, mousepos.y - diff.top),
                    point;

                point = placePoint(coord, 1, function(){
                    triggerPointClick(point);
                    e.preventDefault();
                });

                $('#atlas-objects-add').data('droppedPoint', point);
                showObjectForm({ coord: coord, point: point });
                fillCoords_withGeoData(jPopup, point.coord.lat, point.coord.lon);
                fillAddress_withGeoData(jPopup, point.coord.lat, point.coord.lon);

                PGmap.Events.removeHandler(pgMap.globals.mainElement(), 'mousedown', onMouseDown);
            }

            function placePoint(coord, draggable, onClick) { //place a grey dot on the map when creating new object
                var point = new PGmap.Point({ draggable: draggable, coord: coord });

                if (draggable) {
                    point.draggable.callback("drag", function(position){
                        point.coord = point.globals.xyToLonLat(position.x, position.y);
                        fillCoords_withGeoData(jPopup, point.coord.lat, point.coord.lon);
                    });
                    point.draggable.callback("dragEnd", function(position){
                        fillAddress_withGeoData(jPopup, point.coord.lat, point.coord.lon);
                    });
                }

                pgMap.geometry.add(point);

                return point;
            }
        });

        jPopup.find('.rst-btn, .exit').click(function(){
            var myPoint;

            $('#atlas-objects-add').removeClass('active');
            $('#atlas-objects-add-hint').hide();

            jPopup.hide();
            myPoint = jPopup.data('myPoint');

            if (myPoint.draggable) {
                myPoint.draggable.kill();
                myPoint.draggable = null;
                myPoint.coord = jPopup.data('myPointCoord');
                myPoint.update();

                if (myPoint.balloon) {
                    myPoint.balloon.close();
                }
            }

            if (jPopup.hasClass('add')) {
                pgMap.geometry.remove(myPoint);
            }

            return false;
        });
    }

    function fillAddress_withGeoData(form, lat, lon){
        pgMap.search({ lat: lat, lon: lon, type: 'geocode', lng: pgMap_locale }, function(res){
            //TODO: Set region name into form (make chzn plugin select it). RegionName = pointData.subject_name
            var pointData = JSON.parse(res).res[0],
                address = pointData.addr,
                addressPlaceholder = $('#address', form);

            if (addressPlaceholder.length === 0) {
                console.log('Can\'t find addressPlaceholder!');
            }

            addressPlaceholder.val(address);
        });
    }

    function fillCoords_withGeoData(form, lat, lon){
        var lonFixed = PGmap.Utils.fromMercX(lon).toFixed(6),
            latFixed = PGmap.Utils.fromMercY(lat).toFixed(6),
            lonPlaceholder = $('#lon', form),
            latPlaceholder = $('#lat', form);

        if (latPlaceholder.length === 0) {
            console.log('Can\'t find latPlaceholder!');
        }
        if (lonPlaceholder.length === 0) {
            console.log('Can\'t find lonPlaceholder!');
        }

        latPlaceholder.val(latFixed);
        lonPlaceholder.val(lonFixed);
    }

    function showObjectForm(params) {
        var point = params.point,

            jPopup = $('#add-object-form'),
            jPopupForm = jPopup.find('form'),

            jSuccess = $('#success-object-form'),
            jSuccessForm = jSuccess.find('form'),

            jMyObjectsList = $('#myobj_list'),
            jAddedImages = jPopup.find('.added-images'),
            objectImages,
            image,
            fileUploaderTemplate = $('#file-uploader-template'),
            addedImageTemplate = $('#added-image-template'),
            addObjectFormImages = $('#add-object-form .added-images'),
            entityData = params.entity,

            formObject_id = $('#object-id', jPopupForm),
            formObject_title = $('#title', jPopupForm),
            formObject_announce = $('#announce', jPopupForm),
            formObject_lat = $('#lat', jPopupForm),
            formObject_lon = $('#lon', jPopupForm),
            formObject_primaryCategory = $('#primaryCategory', jPopupForm),
            formObject_secondaryCategories = $('#secondaryCategories', jPopupForm),
            formObject_siteUrl = $('#siteUrl', jPopupForm),
            formObject_email = $('#email', jPopupForm),
            formObject_phone = $('#phone', jPopupForm),
            formObject_regions = $('#regions', jPopupForm),
            formObject_address = $('#address', jPopupForm),
            formObject_workTime = $('#workTime', jPopupForm),
            formObject_weekends = $('#weekends', jPopupForm);

        jPopup.data('myPoint', point);
        jPopup.data('myPointCoord', point.coord);

        jPopupForm.resetForm();
        jAddedImages.empty();

        jPopup.show();

        formObject_primaryCategory.select2();
        formObject_secondaryCategories.select2();
        formObject_regions.select2();
        formObject_weekends.select2();

        new qq.FileUploader({ //Ajax file uploader for images. It works with pure DOM Elements only, so no jQuery objects here!
            element: document.getElementById('file-uploader'),
            action: imageUploadUrl,
            template: fileUploaderTemplate.tmpl({ }).html(),
            onSubmit: function(){
                armdMk.startLoading();
            },
            onComplete: function(id, filename, response){
                armdMk.stopLoading();

                if (response.success) {
                    jAddedImages.append(addedImageTemplate.tmpl(response.result));
                }
                else {
                    alert(response.message);
                }
            }
        });

        if (entityData) {
            jPopup.removeClass('add').addClass('edit');
            formObject_id.val(entityData.id);

            formObject_title.val(entityData.title);
            formObject_announce.val(entityData.announce);
            formObject_lat.val(entityData.lat);
            formObject_lon.val(entityData.lon);

            formObject_primaryCategory.select2('val', entityData.primaryCategory);
            formObject_secondaryCategories.select2('val', entityData.secondaryCategories);

            formObject_siteUrl.val(entityData.siteUrl);
            formObject_email.val(entityData.email);
            formObject_phone.val(entityData.phone);
            formObject_regions.select2('val', entityData.regions);
            formObject_address.val(entityData.address);
            formObject_workTime.val(entityData.workTime);
            formObject_weekends.select2('val', entityData.weekends);

            if (entityData.images && entityData.images.length > 0) {
                objectImages = entityData.images;

                for (var i in objectImages) {
                    if (objectImages.hasOwnProperty(i)) {
                        image = entityData.images[i];
                        addedImageTemplate
                            .tmpl({
                                'id': image.id,
                                'imageUrl': image.thumbUrl
                            })
                            .appendTo(addObjectFormImages);
                    }
                }
            }

            point.draggable = new PGmap.Events.Draggable(point, {
                drag: function(position){
                    point.coord = point.globals.xyToLonLat(position.x, position.y);
                    fillCoords_withGeoData(jPopup, point.coord.lat, point.coord.lon);
                },
                dragEnd: function(position){
                    fillAddress_withGeoData(jPopup, point.coord.lat, point.coord.lon);
                }
            });

        }
        else {
            jPopup.removeClass('edit').addClass('add');
            formObject_id.val('');
        }

        jSuccess.find('.exit').click(function(){
            $('#atlas-objects-add').removeClass('active');
            jSuccess.hide();

            if (point.draggable) {
                point.draggable.kill();
            }

            return false;
        });

        //address and geolocation
        formObject_address.autocomplete({
            source: function(request, response){
                pgMap.search({ q: request.term, type: 'search' }, function(r){
                    var json = $.parseJSON(r),
                        jsonRes = json.res,
                        results = [],
                        el,
                        elMatches,
                        match,
                        obj;

                    if (json.success) {
                        for (var i in jsonRes) {
                            if (jsonRes.hasOwnProperty(i)) {
                                el = jsonRes[i];

                                if (el.type == "addr") {
                                    elMatches = el.matches;

                                    for (var j in elMatches) {
                                        if (elMatches.hasOwnProperty(j)) {
                                            match = el.matches[j];
                                            obj = {
                                                label: match.addr,
                                                value: match.addr,
                                                match: match
                                            };

                                            results.push(obj);
                                        }
                                    }
                                }
                            }
                        }

                        response(results);
                    }
                });
            },
            select: function(event, ui){
                var lon = parseFloat(ui.item.match.x),
                    lat = parseFloat(ui.item.match.y);

                $('#lon').val(lon.toFixed(6));
                $('#lat').val(lat.toFixed(6));

                point.coord.lon = PGmap.Utils.mercX(lon);
                point.coord.lat = PGmap.Utils.mercY(lat);
                point.update();

                pgMap.setCenter(new PGmap.Coord(lon, lat, true));
            }
        }).data('autocomplete');

        //send data to add or update an object
        jPopupForm.ajaxForm({
            dataType: 'json',
            beforeSubmit: function(){
                showLoadingGif();
            },
            success: function(response, statusText, xhr, $form){
                var createdObject;

                hideLoadingGif();

                if (response.success) {
                    jPopup.hide();

                    if (point.draggable) {
                        point.draggable.kill();
                    }

                    createdObject = response.result;
                    pgMap.geometry.remove(point);
                    point = placePoint(createdObject);

                    if (response.result.mode == 'edit') {
                        jSuccess.removeClass('add').addClass('edit');
                    }
                    else if (response.result.mode == 'add') {
                        jSuccess.removeClass('edit').addClass('add');
                    }

                    jSuccess.show();
                    jSuccess.find('input[name=is_public]').removeAttr('checked');
                    jSuccess.find('.object-id').val(createdObject.id);
                    jSuccess.find('.object-title').val(createdObject.title);

                }
                else {
                    alert(response.message);
                }
            }
        });

        //add an object to pgMap
        jSuccessForm.ajaxForm({
            dataType: 'json',
            beforeSubmit: function(){
                showLoadingGif();
            },
            success: function(response, statusText, xhr, $form){
                var objectId,
                    objectTitle,
                    status,
                    statusTitle,
                    jLi;

                hideLoadingGif();

                if (response.success) {
                    $('#atlas-objects-add').removeClass('active');
                    $('#atlas-objects-add-hint').hide();
                    jSuccess.hide();

                    if (jPopup.hasClass('add')) {
                        objectId = response.result.id;
                        objectTitle = response.result.title;
                        status = response.result.status;
                        statusTitle = response.result.statusTitle;
                        jLi = $('#myobj_list_template').tmpl({ 'title': objectTitle });

                        jLi.data('id', objectId);
                        jLi.data('point', point);
                        jLi.find('.moder').removeClass('status0 status1 status2 status3')
                            .addClass('status'+status)
                            .attr('title', statusTitle);
                        jLi.find('.moder span').text(status);
                        jMyObjectsList.append(jLi);
                    }
                    else { //edit
                        objectId = $('#object-id').val();
                        status = response.result.status;
                        statusTitle = response.result.statusTitle;

                        jLi = jMyObjectsList.find('li').filter(function(){
                            return $(this).data('id') == objectId;
                        });

                        jLi.find('span').text($('#name').val());
                        jLi.find('.moder span').text(response.result.status);
                        jLi.find('.moder').removeClass('status0 status1 status2 status3')
                            .addClass('status'+status)
                            .attr('title', statusTitle);
                    }
                }
            }
        });
    }
    /*===== awaiting refactoring ========
    ===================================*/


    function gatherActiveTabFilters_formatted(activeTab) {
        var checkedFilters = $('label.checked > span', activeTab),
            filters = [],
            checkFirstFilterIfNoneChecked = function(){
                if (checkedFilters.length === 0) {
                    checkedFilters = $('label.gray-checked:first > span', activeTab);
                    checkedFilters.parent('label').addClass('checked');
                }
            };

        switch (filterType) {
            case 'filter_culture_objects': //TODO: duplication
                checkFirstFilterIfNoneChecked();

                $.each(checkedFilters, function(){
                    var item = $(this);

                    filters.push(item.attr('data-tag'));
                });
                break;

            case 'filter_tourist_clusters': //TODO: duplication
                checkFirstFilterIfNoneChecked();

                $.each(checkedFilters, function(){
                    var item = $(this);

                    filters.push(item.attr('data-tag'));
                });
                break;

            case 'filter_user_objects':
                //
                break;

            default:
                console.warn('Error gathering filters: Unknown filter type (' + filterType + ')!');

        }

        return filters;
    }

    function showLoadingGif() {
        $('#ajax-loading').show();
    }

    function hideLoadingGif(){
        $('#ajax-loading').hide();
    }


    /*===============
    ==== PUBLIC ===*/
    atlas.init = function(options){
        var regionsSelector = $('#regions-selector select');

        givenObjectId = options && options.objectId ? options.objectId : undefined;
        sendFiltersUrl = options && options.sendFiltersUrl ? options.sendFiltersUrl.toString() : sendFiltersUrl;
        fetchPointDetailsUrl = options && options.fetchPointDetailsUrl ? options.fetchPointDetailsUrl.toString() : fetchPointDetailsUrl;
        fetchMyObjectsUrl = options && options.fetchMyObjectsUrl ? options.fetchMyObjectsUrl.toString() : fetchMyObjectsUrl;
        imageUploadUrl = options && options.imageUploadUrl ? options.imageUploadUrl.toString() : imageUploadUrl;
        deleteMyObjectsUrl = options && options.deleteMyObjectsUrl ? options.deleteMyObjectsUrl.toString() : deleteMyObjectsUrl;

        regionsSelector.chosen({ no_results_text:"Не найдено" }).change(function(){
            pgMap.search({ q: $(this).find('option:selected').text(), type: 'search' }, function(r){
                var json = $.parseJSON(r),
                    bBox,
                    addressBox;

                if (json.success) {
                    bBox = json.res[0].bbox;
                    addressBox = {
                        lon1: PGmap.Utils.mercX(bBox.x1),
                        lon2: PGmap.Utils.mercX(bBox.x2),
                        lat1: PGmap.Utils.mercY(bBox.y1),
                        lat2: PGmap.Utils.mercY(bBox.y2)
                    };

                    pgMap.setCenterByBbox(addressBox);
                }
            });
        });

        initProGorodMap(options.pgMap);
        initLocationFinderAc(options.locationFinderAc);
        if (options.spotlightId) {
            console.log('show particular object');
        }
        else {
            initFilters(options.filterTabs);
        }
        initHacks();
    };

    return atlas;
}());