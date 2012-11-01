var armdLecture = {
    sortBy: 'date',
    page: 1,
    typeCategories: {},
    lectureSuperTypeCode: '',
    inputTimeout: null,

    init: function (typeCategories, lectureSuperTypeCode) {
        armdLecture.typeCategories = typeCategories;
        armdLecture.lectureSuperTypeCode = lectureSuperTypeCode;

        $.manageAjax.create('lifo', {queue: 'clear', maxRequests: 2, abortOld: true});

        armdLecture.checkTypeCategories();

        $('.video-filter').bind('submit', function (event) {
            event.preventDefault();
        });

        // video filter
        $('.video-filter label, .checks-filter label').click(function (e) {
            var targetName = e.target.nodeName.toLowerCase();
            if (targetName === 'span') {
                $(this).closest('label').toggleClass('checked');
                if ($(this).parents('.lecture-type').length) {
                    // if this is lecture type selector
                    armdLecture.checkTypeCategories();
                }
                armdLecture.page = 1;
                armdLecture.loadLectureList();
            }
        });

        // video filter "all"
        $('.video-filter .check_all, .checks-filter .check_all').click(function () {
            var parentDiv = $(this).closest('.simple-filter-block');

            if (!$(this).hasClass('checked')) {
                parentDiv.find('input:checkbox').attr('checked', 'checked');
                parentDiv.find('label').addClass('checked');
                $(this).addClass('checked');

            }
            else {
                parentDiv.find('input:checkbox').removeAttr('checked');
                parentDiv.find('label').removeClass('checked');
                $(this).removeClass('checked');
            }
            armdLecture.page = 1;
//            armdLecture.checkTypeCategories();
            armdLecture.loadLectureList();
        });

        // sort
        $('#lecture-list-container').on('click', '#video_sort li:not(.active) .video-list-sort-by', function () {
            armdLecture.sortBy = $(this).data('sort-by');
            armdLecture.loadLectureList()
        });

        // page
        $('#lecture-list-container').on('click', '.virtual-paginates a', function (event) {
            event.preventDefault();
            armdLecture.page = $(this).data('page');
            armdLecture.loadLectureList();
        });

        // search
        armdLecture.afterDelayedEvent('keyup', '#ss_input', 500, function () {
            armdLecture.loadLectureList();
        });
    },

    initTileUi: function () {
        $('#lecture-list-container').on('mouseenter', '.lecture-tile-list-one-wrap', function () {
            var width = $(this).width(),
                height = $(this).height(),
                liMas = $(this).parents('ul').find('li'),
                thisli = $(this).closest('li');

            $(this).addClass('lecture-tile-hovered');
            $(this).parent().css('height', height);
            $(this).find('.lecture-tile-list-one').css({'width': width - 51, 'height': height - 50});
            //$(this).find('.rusHovered-contacts').css({'width':width -54,'height':height - 50 });
            if (liMas.index(thisli) % 4 == 3) {
                $(this).find('.lecture-tile-hovered-contacts').css({'left': -247}).addClass('left-hand');
                $(this).find('.lecture-tile--list-one').css({'left': 0});
            }
            else {
                $(this).find('.lecture-tile-hovered-contacts').css({'left': width - 1});
            }


        });

        $('#lecture-list-container').on('mouseleave', '.lecture-tile-list-one-wrap', function () {
            $(this).removeClass('lecture-tile-hovered').css({'width': 'auto', 'height': 'auto', 'left': 0});
            $(this).find('.lecture-tile-list-one').css({'width': 'auto', 'height': 'auto', 'left': 0});
            $(this).parent().css({'height': 'auto'});
        });
//
//        $('#search_text').bind('focus', function () {
//            if ($(this).val() === 'Поиск по объектам') {
//                $(this).val('');
//            }
//        });
//
//        $('#search_text').bind('blur', function () {
//            if ($(this).val().length == 0) {
//                $(this).val('Поиск по объектам');
//            }
//        });

//        $('.obr-submit-input').bind('click', function () {
//            armdRussiaImages.loadList();
//        })

    },

    startLoading: function () {
        $('#lecture-list-loading').show();
    },

    stopLoading: function () {
        $('#lecture-list-loading').hide();
    },

    loadLectureList: function () {
        var url = Routing.generate('armd_lecture_list', {
            'lectureSuperTypeCode': armdLecture.lectureSuperTypeCode,
            'page': armdLecture.page
        });
        armdLecture.startLoading();
        $.manageAjax.clear('lifo', true);
        $.manageAjax.add('lifo', {
            // route: armd_lecture_list
            url: url,
            cache: false,
            dataType: 'html',
            type: 'POST',
            data: {
                sortBy: armdLecture.sortBy,
                categories: armdLecture.getSelectedCategories(),
                types: armdLecture.getSelectedTypes(),
                searchString: $('#ss_input').val()
            },
            success: function (data) {
                $('#lecture-list-container').html(data);
				$('.lecture-tile-list-image-frame:first').load(function(){
					imageResize();
				})
                armdLecture.stopLoading();
            }
//            error:function (jqXHR, textStatus, errorThrown) {
//                if(errorThrown !== 'abort') {
//                    alert('Ошибка: ' + errorThrown);
//                    armdLecture.stopLoading();
//                }
//            },
        });
    },

    checkTypeCategories: function () {
        var categories = $('.video-theme');
        categories.find('label').removeClass('checked');
        categories.find('input:checkbox').removeAttr('checked');

        $('.lecture-type label.checked input').each(function (iEl, el) {
            var typeId = $(el).data('lecture-type-id');
            for (i in armdLecture.typeCategories[typeId]) {
                var categoryCheckbox = categories.find('input:checkbox[data-lecture-category-id=' + armdLecture.typeCategories[typeId][i] + ']');
                categoryCheckbox.attr('checked', 'checked');
                categoryCheckbox.closest('label').addClass('checked');
            }
        });
    },

    getSelectedTypes: function () {
        var types = [];
        $('.simple-filter-block.lecture-type label.checked input').each(function (index, el) {
            types.push($(el).data('lecture-type-id'));
        });
        return types;
    },


    getSelectedCategories: function () {
        var categories = [];
        $('.simple-filter-block.video-theme label.checked input').each(function (index, el) {
            categories.push($(el).data('lecture-category-id'));
        });
        return categories;

    },

    afterDelayedEvent: function afterDelayedEvent(eventtype, selector, delay, action) {
        $(selector).bind(eventtype, function () {
            if (typeof(armdLecture.inputTimeout) !== "undefined") {
                clearTimeout(armdLecture.inputTimeout);
            }
            armdLecture.inputTimeout = setTimeout(action, delay);
        });
    }


};