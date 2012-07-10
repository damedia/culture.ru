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

    $('#clear-all').click(function(){
        clearAllPoints();
        return false;
    });

    //------------------------
    // ajax-loading
    function showAjaxLoading() {
        $('#ajax-loading').show();
    }

    function hideAjaxLoading() {
        $('#ajax-loading').hide();
    }

    function clearAllPoints() {
        var objectsAll = map.geometry.get('all');
        $.each(objectsAll.points, function(i,el){
            map.geometry.remove(el);
        });
    }

    function drawPlacemarks(data) {

        var minLng=1000, maxLng=0,
            minLat=1000, maxLat=0;

        $.each(data, function(i, el){
            // place marker
            var customPoint = new PGmap.Point({
                    uid: el.id,
                    coord: new PGmap.Coord(el.lng, el.lat, true),
                    url: bundleImagesUri + '/pin' + el.type + '.png'
                }),
                customBalloon = new PGmap.Balloon({
                    content: el.text,
                    isClosing: true,
                    isHidden: true
                });
            customBalloon.setSize(350, 100);

            customPoint.addBalloon(customBalloon);

            $(customPoint.element).data('uid', el.id);

            PGmap.Events.addHandler(customPoint.element, 'click', function(e){
                var uid = $(customPoint.element).data('uid');

                $.ajax({
                    url: fetchMarkerDetailUri,
                    data: { id: uid },
                    success: function(res) {
                        $(customPoint.balloon.element).find('span').html($('<div class="w"></div>').append(res));
                        customPoint.balloon.show();
                    }
                });

            });

            map.geometry.add(customPoint);

            if (parseFloat(el.lat) > maxLat) maxLat = el.lat;
            if (parseFloat(el.lat) < minLat) minLat = el.lat;
            if (parseFloat(el.lng) > maxLng) maxLng = el.lng;
            if (parseFloat(el.lng) < minLng) minLng = el.lng;
        });

        console.log(minLat, maxLat, minLng, maxLng);
        map.setCenterByBbox({
            lon1: maxLng,
            lon2: minLng,
            lat1: maxLat,
            lat2: minLat
        });
    }

    //-------------------------
    $('#regions-selector').chosen();

    // Скроллбокс для фильтра объектов
    $(window).load(function(){
        $('#map-types-pane').jScrollPane({
            showArrows: true,
            arrowScrollOnHover: true
        });
    });

    // Фильтр по типам объектов
    var cbTypes = $('#filter-types').find('.cb-type');
    cbTypes.on('change', function(){
        $('#filter-types').submit();
    });

    // Отметить/снять отметку со всех галок
    $('.atlas-aside-group-menu a').click(function(){
        if ($(this).hasClass('select')) {
            cbTypes.each(function(i,el){
                $(el).attr('checked', 'checked');
            });
        } else {
            cbTypes.each(function(i,el){
                $(el).removeAttr('checked');
            });
        }
        $('#filter-types').submit();
        return false;
    });

    // Filter form
    $('#filter-types').ajaxForm({
        url: fetchMarkersUri,
        beforeSubmit: function() {
            clearAllPoints();
            showAjaxLoading();
        },
        dataType: 'json',
        success: function(res) {
            hideAjaxLoading();
            if (res.result.length) {
                drawPlacemarks(res.result);
            } else {
                alert('Ничего не найдено.');
            }
        }
    });

    // load and place markers
    $('#filter-types').submit();

    // geocoder
    var geocoder = $('#geocoder'),
        geocoderSearch = geocoder.find('.search'),
        geocoderSubmit = geocoder.find('.submit'),
        geocoderResults = geocoder.find('.results');

    geocoderSubmit.click(function(){
        map.search({
            q: 'Новосибирск',
            type: 'search'
        }, function (res) {
            var r = $.parseJSON(res);
            if (r.success) {
                geocoderResults.empty();

                $.each(r.res, function(i,el){
                    console.log(el);
                    geocoderResults.append(el.subject_name);
                });
            }
        });
    });



});
