var DamediaShare = (function(){
    var share = {};

    share.init = function(options){
        new Ya.share({
            element: 'yandex-share',
            l10n: options.locale,
            theme: 'default',
            elementStyle: {
                type: 'none',
                quickServices: ['vkontakte', 'lj', 'twitter', 'facebook', 'odnoklassniki']
            },
            link: options.link,
            title: options.title,
            description: options.description
        });
    };

    return share;
}());