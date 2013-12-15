var DamediaAddToFavorites = (function(){
    var favorites = {};

    favorites.init = function(options){
        $('.entity-image-footer').on('click', '.user-favorites-button', function(e){
            var $this = $(this),
                action = $this.attr('data-action'),
                htmlReplacement,
                ajaxUrl,
                ajaxData = {
                    type: options.entityType,
                    id: options.entityId,
                    redirect: false
                };

            e.preventDefault();

            if (action === 'add') {
                ajaxUrl = Routing.generate('armd_user_favorites_add');
                htmlReplacement = options.htmlReplacementForAdd;
            }
            else {
                ajaxUrl = Routing.generate('armd_user_favorites_del');
                htmlReplacement = options.htmlReplacementForRemove;
            }

            $.ajax({ url: ajaxUrl, type: 'get', data: ajaxData, dataType: 'html' }).done(function(data){
                if (data == '1') {
                    $this.fadeOut(200, function(){
                        $this.replaceWith(htmlReplacement);
                    });
                }
            });
        });
    };

    return favorites;
}());