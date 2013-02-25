var armdMessager = {
    messageContainer:'#armd_messages',

    showMessage:function (message)
    {
        $(armdMessager.messageContainer)
            .html(message)
            .show()
            .fadeOut(8000, 'swing');
    }
};

