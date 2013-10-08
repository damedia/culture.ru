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
        sendFiltersUrl,
        clusterPoints,
        mapClusterImagesUrl,
        fetchPointDetailsUrl,
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
        tabsChanged = new ObjectCustomEvent(),
        filtersChanged = new ObjectCustomEvent();

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

        tabsChanged.subscribe(tabsChangedCallback);

        filtersChanged.subscribe(function(sender, eventArgs){
            var filtersPlaceholder = eventArgs.filters,
                checkedFilters = filtersPlaceholder.find('label.checked');

            console.log(checkedFilters);
        });
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
            activeTabLinkId,
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
        activeTabLinkId = tabLinks.filter('.active').attr('id');
        tabPanels = $(filters_placeholder).children('div');

        $.each(tabPanels, function(){
            var tab = $(this);

            if (tab.attr('data-type') !== activeTabLinkId) {
                tab.hide();
            }
        });

        tabLinks.on('click', function(event){
            var clickedItem = $(this);

            event.preventDefault();

            if (clickedItem.hasClass('active')) {
                return;
            }

            activeTabLinkId = clickedItem.attr('id');

            tabLinks.removeClass('active');
            clickedItem.addClass('active');

            tabPanels.hide();
            tabPanels.filter('[data-type="' + activeTabLinkId + '"]').show();

            if (typeof history.pushState !== 'undefined') {
                history.pushState(null, document.title, Routing.generate('armd_atlas_index', { filterType: activeTabLinkId }));
            }

            tabsChanged.fire(undefined, { category: gatherCheckedTabFilters_formatted(tabPanels.filter('[data-type="' + activeTabLinkId + '"]')), filterType: activeTabLinkId });
        });

        tabsChanged.fire(undefined, { category: gatherCheckedTabFilters_formatted(tabPanels.filter('[data-type="' + activeTabLinkId + '"]')), filterType: activeTabLinkId });
    }








    function tabsChangedCallback(sender, eventArgs){
        showLoadingGif();

        $.post(sendFiltersUrl, { category: eventArgs.category, filter_type: eventArgs.filterType })
            .done(function(json, textStatus, jqXHR){
                var objects = json.result;

                if (json.success === false) {
                    console.warn('Sending filters failed with response: ' + json.message);
                    return;
                }

                clearMap();

                if (objects && objects.length) {
                    var points = [];

                    for (var i in objects) {
                        var object = objects[i],
                            point = placePoint(objects[i]);

                        if (point && !object.obraz) {
                            points.push(point);
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
                            for (var n = clusterPoints.clusters.length; n--; ) {
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
                }

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

        if (! (object.lon || object.lat)) {
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
        });

        return point;
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








    function gatherCheckedTabFilters_formatted(tab) {
        var checkedFilters = $('label.checked > span', tab),
            result = [];

        if (checkedFilters.length === 0) {
            checkedFilters = $('label.gray-checked:first > span', tab);
            checkedFilters.parent('label').addClass('checked');
        }

        $.each(checkedFilters, function(){
            result.push($(this).attr('data-tag'));
        });

        return result.join(',');
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
        sendFiltersUrl = options && options.sendFiltersUrl ? options.sendFiltersUrl.toString() : '';

        if (sendFiltersUrl === '') {
            console.warn(warn_unableToInitAtlasModule + 'Couldn\'t find send filters URL!\nParameter \'sendFiltersUrl\' is incorrect or undefined!');
            return;
        }

        fetchPointDetailsUrl = options && options.fetchPointDetailsUrl ? options.fetchPointDetailsUrl.toString() : '';

        if (fetchPointDetailsUrl === '') {
            console.warn(warn_unableToInitAtlasModule + 'Couldn\'t find fetch point details URL!\nParameter \'fetchPointDetailsUrl\' is incorrect or undefined!');
            return;
        }

        initProGorodMap(options.pgMap);
        initLocationFinderAc(options.locationFinderAc);
        initFilters(options.filterTabs);
    };

    return atlas;
}());