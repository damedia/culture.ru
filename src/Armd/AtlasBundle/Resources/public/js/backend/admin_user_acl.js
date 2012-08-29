var armdAtlasAdminUserAcl = {
    listUrl: null,

    init:function () {

        $('.atlas-object-acl-link').bind('click', function(event) {
            event.preventDefault();
            var userId = $(this).parents('td').eq('0').attr('objectid');
            armdAtlasAdminUserAcl.openAclDialog(userId);
        });

        $('<div id="atlas-object-acl-dialog-container"></div>').appendTo('body');

        $('#atlas-object-acl-dialog-container').on('click', '', function() {

        });
    },

    openAclDialog:function (userId) {
        var dialog = $('<div style="display: none"></div>')
            .appendTo('#atlas-object-acl-dialog-container');

        var url =  armdAtlasAdminUserAcl.listUrl.replace('0', userId);
        dialog.dialog({
            title: 'Доступные объекты атласа',
            close: function() {
                dialog.remove();
            },
            width: 600,
            height: 400,
            dialogClass: 'loading atlas-object-acl-dialog',
            modal: true
        });
        dialog.load(
            url,
            function () {
                dialog.removeClass('loading');
            }

        )
    }
};