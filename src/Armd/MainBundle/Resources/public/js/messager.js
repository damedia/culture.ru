var armdMessager = {
    messageContainer: '#armd_messages',
    messageTypes:
        {
            'ERROR': 'error',
            'INFO': 'info',
            'WARNING': 'warning'
        }
    ,

    init: function (messageContainerSelector) {
        armdMessager.messageContainer = messageContainerSelector;
        $(armdMessager.messageContainer).find('.armd_messages_close')
            .bind('click', function(event) {
                event.preventDefault();
                armdMessager.closeMessage();
            })
    },

    showMessage: function (message, messageType) {
        if (typeof(messageType) === 'undefined' ) {
            messageType = armdMessager.messageTypes.INFO;
        }

        $(armdMessager.messageContainer)
            .removeClass()
            .addClass(messageType)
            .find('span').html(message).end()
            .show()
            .delay(10000)
            .fadeOut(5000, 'easeInOutExpo');
    },

    closeMessage: function () {
        $(armdMessager.messageContainer)
            .hide();
    }
};

