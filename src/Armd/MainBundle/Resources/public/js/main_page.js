var fetchMarkersUri = Routing.generate('armd_atlas_send_filters'),
    fetchMarkerDetailUri = Routing.generate('armd_atlas_fetch_point_details'),
    fetchClusterDetailUri = Routing.generate('armd_atlas_default_clusterballoon'),
    fetchSideDetailUri = Routing.generate('armd_atlas_default_objectside'),
    bundleImagesUri = armdMk.asset('bundles/armdatlas/images'),
    obrazCategoryId = 74;

var armdMainPage = {
    init: function () {
        armdMainPage.initAtlas();
        armdMainPage.initCommunicationPlatform();
    },

    initAtlas: function () {
        AT.init({
            map: 'map',
            center: [100, 56],
            zoom: 3,
            leftLon: 10,
            rightLon: -190,
            locale: armdMk.locale
        });
    },

    initCommunicationPlatform: function () {
        $('.community-items').on('click', '.vote-minus, .vote-plus', function (event) {
            event.preventDefault();
            var id = $(this).closest('.com-item-one').data('item-id');
            var vote = $(this).hasClass('vote-minus') ? -1 : 1;
            armdMainPage.communicationPlatformVote(id, vote);
        });

        $('#discus').on('click', '#discusitem-tab li', function() {
            armdMainPage.communicationPlatformSelectTab($(this).index());
            return false;
        });

    },

    communicationPlatformSelectTab: function(index) {
        $('.discusitem-content').hide().eq(index).fadeIn();
        $('#discusitem-tab li').removeClass('active').eq(index).addClass('active');

    },

    communicationPlatformVote: function (id, vote) {
        armdMk.startLoadingBlock();
        $.ajax({
            url: Routing.generate('armd_main_communication_platform_request'),
            type: 'post',
            data: {
                restUrl: '/propostal_ajax/',
                method: 'post',
                contentType: 'text/html',
                params: {
                    pid: id,
                    type: vote,
                    action: 'count_like'
                }
            },
            dataType: 'html',
            success: function (data) {
                data = $.trim(data);
                if (data === '1') {
                    armdMessager.showMessage('Ваш голос учтен');
                }
                else if (data === 'no') {
                    armdMessager.showMessage('Вы уже голосовали за это предложение', armdMessager.messageTypes.WARNING)
                }
                else if (data === 'arhiv') {
                    armdMessager.showMessage('Голосование за данное предложение закрыто', armdMessager.messageTypes.WARNING)
                }
                else {
                    armdMessager.showMessage('При голосовании возникла ошибка', armdMessager.messageTypes.ERROR);
                }
            },
            complete: function () {
                armdMainPage.reloadCommunicationPlatform();
                armdMk.stopLoadingBlock();
            }

        });
    },

    reloadCommunicationPlatform: function () {
        var currentTabIndex = $('#discusitem-tab li.active').index();
        armdMk.startLoadingBlock('#discus');
        $.ajax({
            url: Routing.generate('armd_main_latest_topics'),
            type: 'get',
            dataType: 'html',
            success: function (data) {
                $('#discus').html(data);
            },
            complete: function () {
                armdMainPage.communicationPlatformSelectTab(currentTabIndex);
                armdMk.stopLoadingBlock('#discus');
            }
        });
    },

};