var armdAtlasAdminUserAcl = {
    listUrl:null,
    grantUrl:null,
    revokeUrl:null,

    userId:null,

    init:function () {

        $('.atlas-object-acl-link').bind('click', function (event) {
            event.preventDefault();
            var userId = $(this).parents('td').eq('0').attr('objectid');
            armdAtlasAdminUserAcl.createAclDialog(userId);
        });

        $('<div id="atlas-object-acl-dialog-container"></div>').appendTo('body');
    },

    createAclDialog:function (userId) {
        var dialog = $('<div style="display: none;"></div>')
            .appendTo('#atlas-object-acl-dialog-container');

        armdAtlasAdminUserAcl.userId = userId;

        dialog.dialog({
            title:'Доступные объекты атласа',
            close:function () {
                dialog.remove();
            },
            width:600,
            height:400,
            dialogClass:'atlas-object-acl-dialog',
            modal:true
        });
        armdAtlasAdminUserAcl.loadAclDialog(dialog);

    },

    loadAclDialog:function (dialog) {
        dialog.html('');
        dialog.addClass('loading');
        var url = armdAtlasAdminUserAcl.listUrl.replace('~user_id~', armdAtlasAdminUserAcl.userId);
        dialog.load(
            url,
            function (responseText, textStatus, XMLHttpRequest) {
                dialog.removeClass('loading');
                if(textStatus == 'error') {
                    dialog.html('Произошла ошибка. Возможно, пользователь был удален.');
                } else {
                    armdAtlasAdminUserAcl.initAclDialog(dialog);
                }
            }
        )
    },

    revoke:function (dialog, objectId) {
        dialog.html('');
        dialog.addClass('loading');
        var url = armdAtlasAdminUserAcl.revokeUrl;
        url = url.replace('~user_id~', armdAtlasAdminUserAcl.userId);
        url = url.replace('~object_id~', objectId);

        dialog.load(
            url,
            function (responseText, textStatus, XMLHttpRequest) {
                if(textStatus == 'error') {
                    dialog.html('Произошла ошибка. Возможно, пользователь был удален.');
                } else {
                    armdAtlasAdminUserAcl.initAclDialog(dialog);
                }
            }
        )
    },

    grant:function (dialog, objectId) {
        dialog.html('');
        dialog.addClass('loading');
        var url = armdAtlasAdminUserAcl.grantUrl;
        url = url.replace('~user_id~', armdAtlasAdminUserAcl.userId);
        url = url.replace('~object_id~', objectId);

        dialog.load(
            url,
            function (responseText, textStatus, XMLHttpRequest) {
                if(textStatus == 'error') {
                    dialog.html('Произошла ошибка. Возможно, пользователь был удален.');
                } else {
                    armdAtlasAdminUserAcl.initAclDialog(dialog);
                }
            }
        )
    },

    initAclDialog:function (dialog) {
        dialog.removeClass('loading');
        dialog.find('.chzn-select').chosen();

        dialog.find('.atlas-object-acl-add-object').bind('click', function () {
            var objectId = $('.atlas-object-acl-object-select').val();
            if (objectId > 0) {
                armdAtlasAdminUserAcl.grant(dialog, objectId);
            }
        });

        dialog.find('.atlas-object-list a').bind('click', function () {
            var objectId = $(this).data('id');
            var objectName = $(this).parent().find('span').text();
            if (objectId > 0) {
                if (window.confirm('Удалить доступ к "' + objectName + '"')) {
                    armdAtlasAdminUserAcl.revoke(dialog, objectId);
                }
            }
        });
    }

};