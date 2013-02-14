var fetchMarkersUri = Routing.generate('armd_atlas_default_filter'),
    fetchMarkerDetailUri = Routing.generate('armd_atlas_default_objectballoon'),
    fetchClusterDetailUri = Routing.generate('armd_atlas_default_clusterballoon'),
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
    
    peopleCulturePollsModule: 'm_vote',
    
    peopleCulturePollsCount: 1,
    
    peopleCulturePolls: function (data, logged, module, count) {
        try {                            
            var poll = data['data']['items'][0],
                pollBlock = $('#people-culture-polls'),
                iType = poll.VOTE_TYPE == 'single' ? 'radio' : 'checkbox',
                ul = $('<ul></ul>'),
                button = $('<input type="button" value="Готово!">');
            
            armdMainPage.peopleCulturePollsModule = module;
            armdMainPage.peopleCulturePollsCount = count;
            pollBlock.empty();

            //pollBlock.append($('<h3>' + poll.title + '</h3>')); 
            pollBlock.append($('<h3><span>Опрос</span></h3>')); 
            pollBlock.append($('<h4>' + poll.content + '</h4>'));
            pollBlock.append(ul);

            if (poll.already_vote === 0 && logged == 1) {                                            
                for (answer in poll.answers) {
                    var item = '<li><label><input name="answer" type="' + iType 
                        + '" value="' + poll.answers[answer].VOTE_ANSWER_ID + '"> ' 
                        + poll.answers[answer].TITLE + '</label></li>';
                    ul.append($(item));
                }

                pollBlock.append(button); 
                button.click(function () {
                    var votes = [];
                    ul.find('input:checked').each(function(i) {
                        votes.push($(this).attr('value'));
                    });

                    if (votes.length > 0) {
                        armdMainPage.peopleCulturePollsVote(poll.VOTE_ID, votes);
                    }
                });
            } else {
                var max = 0, all = 0, limit = 200, width = 0, isMax = false, item;

                for (answer in poll.answers) {
                    if (parseInt(poll.answers[answer].ANSWER_COUNT) > parseInt(poll.answers[max].ANSWER_COUNT)) {
                        max = answer;
                    }

                    all += parseInt(poll.answers[answer].ANSWER_COUNT);
                }

                for (answer in poll.answers) {
                    if (parseInt(poll.answers[max].ANSWER_COUNT) != 0) {
                        width = Math.floor(limit * parseInt(poll.answers[answer].ANSWER_COUNT) / all);
                    }
                    
                    if (width < 5) {
                        width = 5;
                    }
                    
                    isMax = parseInt(poll.answers[answer].ANSWER_COUNT) == parseInt(poll.answers[max].ANSWER_COUNT) 
                        && parseInt(poll.answers[max].ANSWER_COUNT) != 0;
                    
                    item = '<li><div class="cp_line" style="width: ' + width + 'px;' 
                        + (isMax ? ' background-color: #f79017;' : '') + '"></div><div class="cp_count"' 
                        + (isMax ? 'style="color: #f79017;"' : '') + '>' 
                        + poll.answers[answer].ANSWER_COUNT + '</div><span>' 
                        + poll.answers[answer].TITLE + '</span></li>';
                    ul.append($(item));
                }
            }
        } catch(err) {
            //console.log(err);
        }
    },
    
    reloadPeopleCulturePolls: function () {                
        $.ajax({
            url: Routing.generate('armd_main_communication_platform_request'),
            type: 'get',
            data: {
                restUrl: '/ru/export/',
                method: 'get',
                contentType: 'text/html',
                params: {
                    module: armdMainPage.peopleCulturePollsModule,
                    count: armdMainPage.peopleCulturePollsCount
                }
            },
            dataType: 'json',
            success: function (data) {                
                armdMainPage.peopleCulturePolls(data);               
            }

        });

    },
    
    peopleCulturePollsVote: function (id, votes) {
        armdMk.startLoadingBlock();
        $.ajax({
            url: Routing.generate('armd_main_communication_platform_request'),
            type: 'post',
            data: {
                restUrl: '/ru/ajax_vote/',
                method: 'post',
                contentType: 'text/html',
                params: {
                    vote_id: id,
                    answer_id: votes
                }
            },
            dataType: 'html',
            success: function (data) {
                data = $.trim(data);
                
                if (data === '"added"') {
                    armdMessager.showMessage('Ваш голос учтен');
                } else if (data === '"already_voted"') {
                    armdMessager.showMessage('Вы уже голосовали в этом опросе', armdMessager.messageTypes.WARNING)
                } else if (data === '"invalid data"') {
                    armdMessager.showMessage('Голосование по этому опросу закрыто', armdMessager.messageTypes.WARNING)
                } else if (data === '"no_auth"') {
                    armdMessager.showMessage('Вы не авторизованы', armdMessager.messageTypes.WARNING)
                } else {
                    armdMessager.showMessage('При голосовании возникла ошибка', armdMessager.messageTypes.ERROR);
                }
            },
            complete: function () {
                armdMainPage.reloadPeopleCulturePolls();
                armdMk.stopLoadingBlock();
            }

        });
    }

};